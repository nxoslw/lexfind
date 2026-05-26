<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Lawyer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class LawyerController extends Controller
{
    /**
     * Display a listing of the lawyers.
     */
    public function index(Request $request): Response
    {
        $lawyers = Lawyer::with('cases')->get();

        $name = $request->input('name');
        $practice = $request->input('practice');
        $city = $request->input('city');
        $state = $request->input('state');
        $exp = (int) $request->input('exp', 0);
        $win = (int) $request->input('win', 0);
        $rating = (float) $request->input('rating', 0);
        $avail = $request->input('avail');
        $cert = $request->input('cert');

        $filtered = $lawyers->filter(function ($lawyer) use ($name, $practice, $city, $state, $exp, $win, $rating, $avail, $cert) {
            if ($name && stripos($lawyer->name, $name) === false && stripos($lawyer->firm, $name) === false) {
                return false;
            }
            if ($practice && ! in_array($practice, $lawyer->practice_areas ?? [])) {
                return false;
            }
            if ($city && stripos($lawyer->city, $city) === false && stripos($lawyer->state, $city) === false) {
                return false;
            }
            if ($state && strcasecmp($lawyer->state, $state) !== 0) {
                return false;
            }
            if ($lawyer->years_experience < $exp) {
                return false;
            }
            $winRate = $lawyer->cases_count > 0 ? round(($lawyer->cases_won / $lawyer->cases_count) * 100) : 0;
            if ($winRate < $win) {
                return false;
            }
            if ($lawyer->rating < $rating) {
                return false;
            }
            if ($avail && $lawyer->availability !== $avail) {
                return false;
            }
            if ($cert === 'yes' && ! $lawyer->is_certified) {
                return false;
            }

            return true;
        });

        // Apply Sorting
        $sort = $request->input('sort', 'rating');
        if ($sort === 'rating') {
            $filtered = $filtered->sortByDesc('rating');
        } elseif ($sort === 'exp') {
            $filtered = $filtered->sortByDesc('years_experience');
        } elseif ($sort === 'win') {
            $filtered = $filtered->sortByDesc(fn ($l) => $l->cases_count > 0 ? ($l->cases_won / $l->cases_count) : 0);
        } elseif ($sort === 'cases') {
            $filtered = $filtered->sortByDesc('cases_count');
        }

        return Inertia::render('Browse', [
            'lawyers' => $filtered->values(),
            'filters' => $request->only(['name', 'practice', 'city', 'state', 'exp', 'win', 'rating', 'avail', 'cert', 'sort']),
        ]);
    }

    /**
     * Display the specified lawyer profile.
     */
    public function show(Lawyer $lawyer): Response
    {
        $lawyer->load('cases');

        return Inertia::render('lawyers/Show', [
            'lawyer' => $lawyer,
        ]);
    }

    /**
     * Show the form for editing the assigned lawyer's profile.
     */
    public function editAssigned(Lawyer $lawyer): Response
    {
        $user = auth()->user();

        if ($lawyer->user_id !== $user->id && ! $user->isAdmin()) {
            abort(403, 'You are not authorized to edit this lawyer profile.');
        }

        return Inertia::render('lawyers/Edit', [
            'lawyer' => $lawyer,
        ]);
    }

    /**
     * Update the assigned lawyer profile in storage.
     */
    public function updateAssigned(Request $request, Lawyer $lawyer): RedirectResponse
    {
        $user = auth()->user();

        if ($lawyer->user_id !== $user->id && ! $user->isAdmin()) {
            abort(403, 'You are not authorized to edit this lawyer profile.');
        }

        $validated = $request->validate([
            'bio' => 'required|string',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:50',
            'website' => 'required|string|max:255',
            'linkedin' => 'nullable|string|max:255',
            'availability' => 'required|string|in:available,busy,unavailable',
        ]);

        $original = $lawyer->getOriginal();
        // Securely update only the self-managed profile settings.
        // Restricted fields (metrics, rating, user_id assignment) remain untouched.
        $lawyer->update($validated);
        $changes = $lawyer->getChanges();

        if (! empty($changes)) {
            AuditLog::create([
                'user_id' => auth()->id(),
                'user_name' => auth()->user()->name,
                'lawyer_id' => $lawyer->id,
                'lawyer_name' => $lawyer->name,
                'action' => 'update',
                'details' => json_encode([
                    'old' => array_intersect_key($original, $changes),
                    'new' => $changes,
                ]),
            ]);
        }

        return redirect()->route('lawyer.edit', $lawyer->slug)->with('success', 'Profile updated successfully.');
    }
}
