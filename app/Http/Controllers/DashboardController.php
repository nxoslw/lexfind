<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Lawyer;
use App\Models\LawyerCase;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    /**
     * Display the dedicated dashboard.
     */
    public function index(Request $request): Response|RedirectResponse
    {
        $user = auth()->user();

        // Safety check, though handled by middleware redirect in routes
        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        // Load all assigned lawyer profiles
        $assignedLawyers = Lawyer::where('user_id', $user->id)->get();
        $lawyer = $assignedLawyers->first();

        if ($user->system === 'bip') {
            // Front Mod Dashboard
            $totalLawyers = Lawyer::count();
            $certifiedCount = Lawyer::where('is_certified', true)->count();
            $availableCount = Lawyer::where('availability', 'available')->count();
            $averageRating = (float) Lawyer::avg('rating');

            // All lawyers in the database (for moderation queue)
            $lawyers = Lawyer::with('user')->get();

            return Inertia::render('Dashboard', [
                'role' => 'bip',
                'lawyer' => $lawyer,
                'assignedLawyers' => $assignedLawyers,
                'stats' => [
                    'total_lawyers' => $totalLawyers,
                    'certified_count' => $certifiedCount,
                    'available_count' => $availableCount,
                    'average_rating' => round($averageRating, 2),
                ],
                'lawyers' => $lawyers,
            ]);
        }

        // Regular User (system = god / default)
        // Top-rated available lawyers (rating >= 4.8, available)
        $featuredLawyers = Lawyer::where('rating', '>=', 4.8)
            ->where('availability', 'available')
            ->take(3)
            ->get();

        // Recent cases handled in the system
        $recentCases = LawyerCase::with('lawyer')
            ->orderBy('year', 'desc')
            ->orderBy('created_at', 'desc')
            ->take(3)
            ->get();

        // Overall directory stats
        $totalCertified = Lawyer::where('is_certified', true)->count();
        $totalCases = LawyerCase::count();

        return Inertia::render('Dashboard', [
            'role' => 'god',
            'lawyer' => $lawyer,
            'assignedLawyers' => $assignedLawyers,
            'stats' => [
                'total_certified' => $totalCertified,
                'total_cases' => $totalCases,
            ],
            'featuredLawyers' => $featuredLawyers,
            'recentCases' => $recentCases,
        ]);
    }

    /**
     * Display the assigned attorney profiles dashboard.
     */
    public function myProfiles(Request $request): Response|RedirectResponse
    {
        $user = auth()->user();

        // Load all assigned lawyer profiles
        $assignedLawyers = Lawyer::where('user_id', $user->id)->get();
        $lawyer = $assignedLawyers->first();

        // Must be front mod or have assigned profiles
        if ($user->system !== 'bip' && $assignedLawyers->isEmpty()) {
            abort(403, 'Unauthorized action.');
        }

        return Inertia::render('lawyers/MyProfiles', [
            'role' => $user->system,
            'lawyer' => $lawyer,
            'assignedLawyers' => $assignedLawyers,
        ]);
    }

    /**
     * Toggle the board certified status of a lawyer profile.
     */
    public function toggleCertified(Request $request, Lawyer $lawyer): RedirectResponse
    {
        $user = auth()->user();
        if ($user->system !== 'bip' && ! $user->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        $original = $lawyer->getOriginal();
        $lawyer->update([
            'is_certified' => ! $lawyer->is_certified,
        ]);
        $changes = $lawyer->getChanges();

        if (! empty($changes)) {
            AuditLog::create([
                'user_id' => $user->id,
                'user_name' => $user->name,
                'lawyer_id' => $lawyer->id,
                'lawyer_name' => $lawyer->name,
                'action' => 'update',
                'details' => json_encode([
                    'old' => array_intersect_key($original, $changes),
                    'new' => $changes,
                ]),
            ]);
        }

        return redirect()->back()->with('success', 'Lawyer certification status updated successfully.');
    }
}
