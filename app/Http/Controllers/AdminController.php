<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Lawyer;
use App\Models\LawyerCase;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Inertia\Response;

class AdminController extends Controller
{
    /**
     * Display the admin dashboard statistics and audit trail logs.
     */
    public function index(): Response
    {
        $totalLawyers = Lawyer::count();
        $assignedLawyers = Lawyer::whereNotNull('user_id')->count();
        $unassignedLawyers = Lawyer::whereNull('user_id')->count();

        $currentUser = auth()->user();
        if ($currentUser->system === 'simp') {
            $totalUsers = User::where('system', '!=', 'ghost')->count();
            $privilegedUsers = User::whereIn('system', ['simp', 'bat', 'bip'])->count();
            $bannedUsers = User::whereNotNull('banned')
                ->where('banned', '!=', '')
                ->where('system', '!=', 'ghost')
                ->count();
        } elseif ($currentUser->system === 'bat') {
            $totalUsers = User::whereNotIn('system', ['ghost', 'simp'])->count();
            $privilegedUsers = User::whereIn('system', ['bat', 'bip'])->count();
            $bannedUsers = User::whereNotNull('banned')
                ->where('banned', '!=', '')
                ->whereNotIn('system', ['ghost', 'simp'])
                ->count();
        } else {
            $totalUsers = User::count();
            $privilegedUsers = User::whereIn('system', ['ghost', 'simp', 'bat', 'bip'])->count();
            $bannedUsers = User::whereNotNull('banned')->where('banned', '!=', '')->count();
        }

        // Load recent audit logs only if super admin (ghost) or site owner (simp)
        $auditLogs = [];
        $userSystem = auth()->user()->system;
        if (in_array($userSystem, ['ghost', 'simp'])) {
            $auditLogs = AuditLog::with(['user', 'lawyer'])
                ->orderBy('created_at', 'desc')
                ->take(10)
                ->get();
        }

        return Inertia::render('admin/Dashboard', [
            'stats' => [
                'total_lawyers' => $totalLawyers,
                'assigned_lawyers' => $assignedLawyers,
                'unassigned_lawyers' => $unassignedLawyers,
                'total_users' => $totalUsers,
                'privileged_users' => $privilegedUsers,
                'banned_users' => $bannedUsers,
            ],
            'auditLogs' => $auditLogs,
        ]);
    }

    /**
     * Display a listing of audit logs with search, time filtering, and pagination.
     */
    public function auditLogsIndex(Request $request): Response
    {
        $userSystem = auth()->user()->system;
        if (!in_array($userSystem, ['ghost', 'simp'])) {
            abort(403, 'Unauthorized action.');
        }

        $search = $request->input('search');
        $timeFilter = $request->input('time', 'all');
        $perPage = (int) $request->input('per_page', 25);
        if (!in_array($perPage, [25, 50, 100, 200])) {
            $perPage = 25;
        }

        $query = AuditLog::with(['user', 'lawyer'])->orderBy('created_at', 'desc');

        // Search Filter
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('user_name', 'like', '%' . $search . '%')
                  ->orWhere('action', 'like', '%' . $search . '%')
                  ->orWhere('lawyer_name', 'like', '%' . $search . '%');
            });
        }

        // Time Filter
        if ($timeFilter !== 'all') {
            $date = match ($timeFilter) {
                'today' => now()->startOfDay(),
                '3_days' => now()->subDays(3),
                '7_days' => now()->subDays(7),
                '15_days' => now()->subDays(15),
                '30_days' => now()->subDays(30),
                '60_days' => now()->subDays(60),
                '90_days' => now()->subDays(90),
                '365_days' => now()->subDays(365),
                default => null,
            };

            if ($date) {
                $query->where('created_at', '>=', $date);
            }
        }

        $auditLogs = $query->paginate($perPage)->withQueryString();

        return Inertia::render('admin/AuditLogs', [
            'auditLogs' => $auditLogs,
            'filters' => [
                'search' => $search,
                'time' => $timeFilter,
                'per_page' => $perPage,
            ],
        ]);
    }

    /**
     * Display a listing of lawyers for management.
     */
    public function lawyersIndex(): Response
    {
        $lawyers = Lawyer::with(['user', 'cases.lawyers'])->get();
        // Load all users who are either god (attorney) or bip (front mod) so they can be assigned
        $users = User::whereIn('system', ['god', 'bip'])->get();

        return Inertia::render('admin/Lawyers', [
            'lawyers' => $lawyers,
            'users' => $users,
        ]);
    }

    /**
     * Store a newly created lawyer profile.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'firm' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:10',
            'specialty' => 'required|string|max:255',
            'bio' => 'required|string',
            'avatar_color' => 'required|string|max:30',
            'initials' => 'required|string|max:4',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:50',
            'website' => 'required|string|max:255',
            'linkedin' => 'nullable|string|max:255',
            'years_experience' => 'required|integer|min:0',
            'cases_count' => 'required|integer|min:0',
            'cases_won' => 'required|integer|min:0',
            'cases_lost' => 'required|integer|min:0',
            'cases_settled' => 'required|integer|min:0',
            'cases_active' => 'required|integer|min:0',
            'financial_recovery' => 'nullable|string|max:255',
            'fee_structure' => 'nullable|string|max:255',
            'is_certified' => 'required|boolean',
            'rating' => 'required|numeric|min:0|max:5',
            'availability' => 'required|string|in:available,busy,unavailable',
            'user_id' => 'nullable|exists:users,id',
            'practice_areas' => 'required|array',
            'criminal_record' => 'required|string|max:255',
            'bar_discipline' => 'required|string|max:255',
            'trial_style' => 'nullable|string',
            'peer_reviews' => 'nullable|array',
            'peer_reviews.rating' => 'nullable|string|max:255',
            'peer_reviews.source' => 'nullable|string|max:255',
            'peer_reviews.quote' => 'nullable|string',
            'peer_reviews.author' => 'nullable|string|max:255',
            'trial_style_details' => 'nullable|array',
            'trial_style_details.approach' => 'nullable|string',
            'trial_style_details.forensics' => 'nullable|string',
            'trial_style_details.global' => 'nullable|string',
            'recent_activity' => 'nullable|array',
            'recent_activity.*.date' => 'nullable|string|max:255',
            'recent_activity.*.title' => 'nullable|string|max:255',
            'recent_activity.*.desc' => 'nullable|string',
            'cases' => 'nullable|array',
            'cases.*.id' => 'nullable|integer',
            'cases.*.name' => 'required|string|max:255',
            'cases.*.case_number' => 'required|string|max:255',
            'cases.*.jurisdiction' => 'required|string|max:255',
            'cases.*.type' => 'required|string|max:30',
            'cases.*.type_label' => 'required|string|max:255',
            'cases.*.status' => 'required|string|in:decided,settled,active',
            'cases.*.year' => 'required|integer',
            'cases.*.court' => 'required|string|max:255',
            'cases.*.won_party' => 'nullable|string|max:255',
            'cases.*.motions_count' => 'nullable|integer',
            'cases.*.motion_success_rate' => 'nullable|string|max:10',
            'cases.*.summary' => 'nullable|string',
            'cases.*.key_finding' => 'nullable|string',
            'cases.*.timeline' => 'nullable|array',
            'cases.*.motions' => 'nullable|array',
            'cases.*.parties' => 'nullable|array',
            'cases.*.issues' => 'nullable|array',
            'cases.*.documents' => 'nullable|array',
            'cases.*.rate_boxes' => 'nullable|array',
            'cases.*.next_steps' => 'nullable|array',
            'cases.*.lawyers' => 'nullable|array',
            'cases.*.lawyers.*.id' => 'required|exists:lawyers,id',
            'cases.*.lawyers.*.outcome' => 'required|string|in:won,lost,settled,active',
        ]);

        $casesData = $request->input('cases', []);
        $validatedLawyer = collect($validated)->except('cases')->toArray();

        $lawyer = Lawyer::create($validatedLawyer);

        $lawyersToRecalculate = collect([$lawyer->id]);

        // Create associated cases
        foreach ($casesData as $caseData) {
            $casePayload = collect($caseData)->except(['id', 'lawyers'])->toArray();
            $caseLawyers = $caseData['lawyers'] ?? [];
            $firstLawyerId = !empty($caseLawyers) ? $caseLawyers[0]['id'] : $lawyer->id;
            $casePayload['lawyer_id'] = $firstLawyerId;

            $case = LawyerCase::create($casePayload);

            // Sync lawyers
            $syncData = [];
            $hasCurrentLawyer = false;
            foreach ($caseLawyers as $cl) {
                $syncData[$cl['id']] = ['outcome' => $cl['outcome']];
                if ($cl['id'] == $lawyer->id) {
                    $hasCurrentLawyer = true;
                }
            }
            if (!$hasCurrentLawyer) {
                $syncData[$lawyer->id] = ['outcome' => 'active'];
            }

            $case->lawyers()->sync($syncData);
            $lawyersToRecalculate = $lawyersToRecalculate->merge(array_keys($syncData));
        }

        // Recalculate statistics for all affected lawyers
        $uniqueLawyerIds = $lawyersToRecalculate->unique()->filter()->toArray();
        foreach ($uniqueLawyerIds as $affectedLawyerId) {
            $affectedLawyer = Lawyer::find($affectedLawyerId);
            if ($affectedLawyer) {
                $affectedLawyer->recalculateStatistics();
            }
        }

        // Audit logging
        AuditLog::create([
            'user_id' => auth()->id(),
            'user_name' => auth()->user()->name,
            'lawyer_id' => $lawyer->id,
            'lawyer_name' => $lawyer->name,
            'action' => 'create',
            'details' => json_encode(['fields' => $validated]),
        ]);

        return redirect()->route('admin.lawyers.index')->with('success', 'Lawyer profile created successfully.');
    }

    /**
     * Update the specified lawyer profile, including user assignments.
     */
    public function update(Request $request, string $id): RedirectResponse
    {
        $lawyer = Lawyer::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'firm' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:10',
            'specialty' => 'required|string|max:255',
            'bio' => 'required|string',
            'avatar_color' => 'required|string|max:30',
            'initials' => 'required|string|max:4',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:50',
            'website' => 'required|string|max:255',
            'linkedin' => 'nullable|string|max:255',
            'years_experience' => 'required|integer|min:0',
            'cases_count' => 'required|integer|min:0',
            'cases_won' => 'required|integer|min:0',
            'cases_lost' => 'required|integer|min:0',
            'cases_settled' => 'required|integer|min:0',
            'cases_active' => 'required|integer|min:0',
            'financial_recovery' => 'nullable|string|max:255',
            'fee_structure' => 'nullable|string|max:255',
            'is_certified' => 'required|boolean',
            'rating' => 'required|numeric|min:0|max:5',
            'availability' => 'required|string|in:available,busy,unavailable',
            'user_id' => 'nullable|exists:users,id',
            'practice_areas' => 'required|array',
            'criminal_record' => 'required|string|max:255',
            'bar_discipline' => 'required|string|max:255',
            'trial_style' => 'nullable|string',
            'peer_reviews' => 'nullable|array',
            'peer_reviews.rating' => 'nullable|string|max:255',
            'peer_reviews.source' => 'nullable|string|max:255',
            'peer_reviews.quote' => 'nullable|string',
            'peer_reviews.author' => 'nullable|string|max:255',
            'trial_style_details' => 'nullable|array',
            'trial_style_details.approach' => 'nullable|string',
            'trial_style_details.forensics' => 'nullable|string',
            'trial_style_details.global' => 'nullable|string',
            'recent_activity' => 'nullable|array',
            'recent_activity.*.date' => 'nullable|string|max:255',
            'recent_activity.*.title' => 'nullable|string|max:255',
            'recent_activity.*.desc' => 'nullable|string',
            'cases' => 'nullable|array',
            'cases.*.id' => 'nullable|integer',
            'cases.*.name' => 'required|string|max:255',
            'cases.*.case_number' => 'required|string|max:255',
            'cases.*.jurisdiction' => 'required|string|max:255',
            'cases.*.type' => 'required|string|max:30',
            'cases.*.type_label' => 'required|string|max:255',
            'cases.*.status' => 'required|string|in:decided,settled,active',
            'cases.*.year' => 'required|integer',
            'cases.*.court' => 'required|string|max:255',
            'cases.*.won_party' => 'nullable|string|max:255',
            'cases.*.motions_count' => 'nullable|integer',
            'cases.*.motion_success_rate' => 'nullable|string|max:10',
            'cases.*.summary' => 'nullable|string',
            'cases.*.key_finding' => 'nullable|string',
            'cases.*.timeline' => 'nullable|array',
            'cases.*.motions' => 'nullable|array',
            'cases.*.parties' => 'nullable|array',
            'cases.*.issues' => 'nullable|array',
            'cases.*.documents' => 'nullable|array',
            'cases.*.rate_boxes' => 'nullable|array',
            'cases.*.next_steps' => 'nullable|array',
            'cases.*.lawyers' => 'nullable|array',
            'cases.*.lawyers.*.id' => 'required|exists:lawyers,id',
            'cases.*.lawyers.*.outcome' => 'required|string|in:won,lost,settled,active',
        ]);

        $casesData = $request->input('cases', []);
        $validatedLawyer = collect($validated)->except('cases')->toArray();

        $original = $lawyer->getOriginal();
        $lawyer->update($validatedLawyer);
        $changes = $lawyer->getChanges();

        // Synchronize cases
        $existingCaseIds = collect($casesData)->pluck('id')->filter()->toArray();

        // Find which lawyers need statistics recalculation before deleting or modifying cases
        $casesToDelete = $lawyer->cases()->whereNotIn('lawyer_cases.id', $existingCaseIds)->get();
        $lawyersToRecalculate = collect([$lawyer->id]);
        foreach ($casesToDelete as $caseToDelete) {
            $lawyersToRecalculate = $lawyersToRecalculate->merge($caseToDelete->lawyers()->pluck('lawyers.id'));
        }

        // Delete cases
        $lawyer->cases()->whereNotIn('lawyer_cases.id', $existingCaseIds)->delete();

        foreach ($casesData as $caseData) {
            $casePayload = collect($caseData)->except(['id', 'lawyers'])->toArray();
            $caseLawyers = $caseData['lawyers'] ?? [];
            $firstLawyerId = !empty($caseLawyers) ? $caseLawyers[0]['id'] : $lawyer->id;
            $casePayload['lawyer_id'] = $firstLawyerId;

            if (!empty($caseData['id'])) {
                $case = LawyerCase::find($caseData['id']);
                if ($case) {
                    $lawyersToRecalculate = $lawyersToRecalculate->merge($case->lawyers()->pluck('lawyers.id'));
                    $case->update($casePayload);
                }
            } else {
                $case = LawyerCase::create($casePayload);
            }

            if ($case) {
                // Sync lawyers
                $syncData = [];
                $hasCurrentLawyer = false;
                foreach ($caseLawyers as $cl) {
                    $syncData[$cl['id']] = ['outcome' => $cl['outcome']];
                    if ($cl['id'] == $lawyer->id) {
                        $hasCurrentLawyer = true;
                    }
                }
                if (!$hasCurrentLawyer) {
                    $syncData[$lawyer->id] = ['outcome' => 'active'];
                }

                $case->lawyers()->sync($syncData);
                $lawyersToRecalculate = $lawyersToRecalculate->merge(array_keys($syncData));
            }
        }

        // Recalculate statistics for all affected lawyers
        $uniqueLawyerIds = $lawyersToRecalculate->unique()->filter()->toArray();
        foreach ($uniqueLawyerIds as $affectedLawyerId) {
            $affectedLawyer = Lawyer::find($affectedLawyerId);
            if ($affectedLawyer) {
                $affectedLawyer->recalculateStatistics();
            }
        }

        // Determine if it was an assignment/unassignment or regular update
        $action = 'update';
        if (array_key_exists('user_id', $changes)) {
            $action = $lawyer->user_id ? 'assign' : 'unassign';
        }

        if (!empty($changes)) {
            AuditLog::create([
                'user_id' => auth()->id(),
                'user_name' => auth()->user()->name,
                'lawyer_id' => $lawyer->id,
                'lawyer_name' => $lawyer->name,
                'action' => $action,
                'details' => json_encode([
                    'old' => array_intersect_key($original, $changes),
                    'new' => $changes,
                ]),
            ]);
        }

        return redirect()->route('admin.lawyers.index')->with('success', 'Lawyer profile updated successfully.');
    }

    /**
     * Remove the specified lawyer profile from storage.
     */
    public function destroy(string $id): RedirectResponse
    {
        // Enforce role permissions: only ghost and simp can delete lawyer data
        if (!in_array(auth()->user()->system, ['ghost', 'simp'])) {
            abort(403, 'Only the Admin or Site Owner is authorized to delete lawyer profiles.');
        }

        $lawyer = Lawyer::findOrFail($id);

        AuditLog::create([
            'user_id' => auth()->id(),
            'user_name' => auth()->user()->name,
            'lawyer_id' => $lawyer->id,
            'lawyer_name' => $lawyer->name,
            'action' => 'delete',
            'details' => json_encode(['fields' => $lawyer->toArray()]),
        ]);

        $lawyer->delete();

        return redirect()->route('admin.lawyers.index')->with('success', 'Lawyer profile deleted successfully.');
    }

    /**
     * Display a listing of all users for admin management.
     */
    public function usersIndex(): Response
    {
        $currentUser = auth()->user();
        if (!in_array($currentUser->system, ['ghost', 'simp', 'bat'])) {
            abort(403, 'Unauthorized action.');
        }

        if ($currentUser->system === 'simp') {
            $users = User::where('system', '!=', 'ghost')->get();
        } elseif ($currentUser->system === 'bat') {
            $users = User::whereNotIn('system', ['ghost', 'simp'])->get();
        } else {
            $users = User::all();
        }

        return Inertia::render('admin/Users', [
            'users' => $users,
        ]);
    }

    /**
     * Store a newly created user in storage.
     */
    public function userStore(Request $request): RedirectResponse
    {
        $currentUser = auth()->user();
        if (!in_array($currentUser->system, ['ghost', 'simp', 'bat'])) {
            abort(403, 'Unauthorized action.');
        }

        $allowedRoles = ['god', 'bip', 'bat'];
        if ($currentUser->system === 'ghost' || $currentUser->system === 'simp') {
            $allowedRoles[] = 'simp';
        }
        if ($currentUser->system === 'ghost') {
            $allowedRoles[] = 'ghost';
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:8',
            'system' => 'required|string|in:' . implode(',', $allowedRoles),
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'system' => $validated['system'],
        ]);

        return redirect()->route('admin.users.index')->with('success', 'User account created successfully.');
    }

    /**
     * Display the specified user profile details, including assigned lawyers and metadata.
     */
    public function userShow(Request $request, string $id): Response
    {
        $currentUser = auth()->user();
        if (!in_array($currentUser->system, ['ghost', 'simp', 'bat'])) {
            abort(403, 'Unauthorized action.');
        }

        $user = User::with('lawyers')->findOrFail($id);

        if ($currentUser->system === 'simp' && $user->system === 'ghost') {
            abort(403, 'Unauthorized action.');
        }

        if ($currentUser->system === 'bat' && in_array($user->system, ['ghost', 'simp'])) {
            abort(403, 'Unauthorized action.');
        }

        $sessions = \DB::table('sessions')
            ->where('user_id', $user->id)
            ->orderBy('last_activity', 'desc')
            ->get()
            ->map(function ($session) use ($request) {
                return [
                    'ip_address' => $session->ip_address,
                    'user_agent' => $session->user_agent,
                    'last_activity' => $session->last_activity,
                    'is_current' => $session->id === $request->session()->getId(),
                ];
            });

        return Inertia::render('admin/UserShow', [
            'user' => $user,
            'sessions' => $sessions,
        ]);
    }

    /**
     * Update the specified user in storage.
     */
    public function userUpdate(Request $request, string $id): RedirectResponse
    {
        $currentUser = auth()->user();
        if (!in_array($currentUser->system, ['ghost', 'simp', 'bat'])) {
            abort(403, 'Unauthorized action.');
        }

        $user = User::findOrFail($id);

        if ($currentUser->system === 'simp' && $user->system === 'ghost') {
            abort(403, 'Unauthorized action.');
        }

        if ($currentUser->system === 'bat' && in_array($user->system, ['ghost', 'simp'])) {
            abort(403, 'Unauthorized action.');
        }

        $allowedRoles = ['god', 'bip', 'bat'];
        if ($currentUser->system === 'ghost' || $currentUser->system === 'simp') {
            $allowedRoles[] = 'simp';
        }
        if ($currentUser->system === 'ghost') {
            $allowedRoles[] = 'ghost';
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'system' => 'required|string|in:' . implode(',', $allowedRoles),
            'banned' => 'nullable|string',
            'password' => 'nullable|string|min:8',
        ]);

        // Safeguard: Prevent altering own role or banning oneself
        if ($user->id === auth()->id()) {
            if ($validated['system'] !== $user->system) {
                return back()->withErrors(['system' => 'You cannot modify your own administrative role.']);
            }
            if (!empty($validated['banned'])) {
                return back()->withErrors(['banned' => 'You cannot ban your own active account.']);
            }
        }

        $updateData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'system' => $validated['system'],
            'banned' => $validated['banned'],
        ];

        if (!empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $user->update($updateData);

        return redirect()->route('admin.users.index')->with('success', 'User account updated successfully.');
    }

    /**
     * Perform an administrative password reset.
     */
    public function userResetPassword(Request $request, string $id): RedirectResponse
    {
        $currentUser = auth()->user();
        if (!in_array($currentUser->system, ['ghost', 'simp', 'bat'])) {
            abort(403, 'Unauthorized action.');
        }

        $user = User::findOrFail($id);

        if ($currentUser->system === 'simp' && $user->system === 'ghost') {
            abort(403, 'Unauthorized action.');
        }

        if ($currentUser->system === 'bat' && in_array($user->system, ['ghost', 'simp'])) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('admin.users.show', $user->id)->with('success', 'User password has been reset successfully.');
    }

    /**
     * Remove the specified user from storage.
     */
    public function userDestroy(string $id): RedirectResponse
    {
        $currentUser = auth()->user();
        if (!in_array($currentUser->system, ['ghost', 'simp', 'bat'])) {
            abort(403, 'Unauthorized action.');
        }

        $user = User::findOrFail($id);

        if ($currentUser->system === 'simp' && $user->system === 'ghost') {
            abort(403, 'Unauthorized action.');
        }

        if ($currentUser->system === 'bat' && in_array($user->system, ['ghost', 'simp'])) {
            abort(403, 'Unauthorized action.');
        }

        // Safeguard: Prevent self-deletion
        if ($user->id === auth()->id()) {
            abort(403, 'You cannot delete your own active administrator account.');
        }

        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'User account deleted successfully.');
    }

    /**
     * Display a listing of cases for management.
     */
    public function casesIndex(): Response
    {
        $cases = LawyerCase::with(['lawyer', 'lawyers'])->get();
        $lawyers = Lawyer::select('id', 'name', 'firm', 'avatar_color', 'initials')->get();

        return Inertia::render('admin/Cases', [
            'cases' => $cases,
            'lawyers' => $lawyers,
        ]);
    }

    /**
     * Store a newly created case.
     */
    public function caseStore(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'lawyer_id' => 'nullable|exists:lawyers,id',
            'lawyers' => 'required|array|min:1',
            'lawyers.*.id' => 'required|exists:lawyers,id',
            'lawyers.*.outcome' => 'required|string|in:won,lost,settled,active',
            'name' => 'required|string|max:255',
            'case_number' => 'required|string|max:255',
            'jurisdiction' => 'required|string|max:255',
            'type' => 'required|string|max:30',
            'type_label' => 'required|string|max:255',
            'status' => 'required|string|in:decided,settled,active',
            'year' => 'required|integer',
            'court' => 'required|string|max:255',
            'won_party' => 'nullable|string|max:255',
            'motions_count' => 'nullable|integer',
            'motion_success_rate' => 'nullable|string|max:10',
            'summary' => 'nullable|string',
            'key_finding' => 'nullable|string',
            'timeline' => 'nullable|array',
            'motions' => 'nullable|array',
            'parties' => 'nullable|array',
            'issues' => 'nullable|array',
            'documents' => 'nullable|array',
            'rate_boxes' => 'nullable|array',
            'next_steps' => 'nullable|array',
        ]);

        $lawyersData = $request->input('lawyers', []);
        $firstLawyerId = !empty($lawyersData) ? $lawyersData[0]['id'] : null;

        $casePayload = collect($validated)->except('lawyers')->toArray();
        if (empty($casePayload['lawyer_id'])) {
            $casePayload['lawyer_id'] = $firstLawyerId;
        }

        $case = LawyerCase::create($casePayload);

        // Sync lawyers
        $syncData = [];
        foreach ($lawyersData as $lData) {
            $syncData[$lData['id']] = ['outcome' => $lData['outcome']];
        }
        $case->lawyers()->sync($syncData);

        // Recalculate stats for all synced lawyers
        foreach (array_keys($syncData) as $lawyerId) {
            $lawyer = Lawyer::find($lawyerId);
            if ($lawyer) {
                $lawyer->recalculateStatistics();
            }
        }

        // Audit logging
        AuditLog::create([
            'user_id' => auth()->id(),
            'user_name' => auth()->user()->name,
            'lawyer_id' => $case->lawyer_id,
            'lawyer_name' => $case->lawyer?->name ?? 'Unknown',
            'action' => 'create_case',
            'details' => json_encode(['fields' => $validated]),
        ]);

        return redirect()->route('admin.cases.index')->with('success', 'Case record created successfully.');
    }

    /**
     * Update the specified case.
     */
    public function caseUpdate(Request $request, string $id): RedirectResponse
    {
        $case = LawyerCase::findOrFail($id);

        $validated = $request->validate([
            'lawyer_id' => 'nullable|exists:lawyers,id',
            'lawyers' => 'required|array|min:1',
            'lawyers.*.id' => 'required|exists:lawyers,id',
            'lawyers.*.outcome' => 'required|string|in:won,lost,settled,active',
            'name' => 'required|string|max:255',
            'case_number' => 'required|string|max:255',
            'jurisdiction' => 'required|string|max:255',
            'type' => 'required|string|max:30',
            'type_label' => 'required|string|max:255',
            'status' => 'required|string|in:decided,settled,active',
            'year' => 'required|integer',
            'court' => 'required|string|max:255',
            'won_party' => 'nullable|string|max:255',
            'motions_count' => 'nullable|integer',
            'motion_success_rate' => 'nullable|string|max:10',
            'summary' => 'nullable|string',
            'key_finding' => 'nullable|string',
            'timeline' => 'nullable|array',
            'motions' => 'nullable|array',
            'parties' => 'nullable|array',
            'issues' => 'nullable|array',
            'documents' => 'nullable|array',
            'rate_boxes' => 'nullable|array',
            'next_steps' => 'nullable|array',
        ]);

        $lawyersData = $request->input('lawyers', []);
        $firstLawyerId = !empty($lawyersData) ? $lawyersData[0]['id'] : null;

        $casePayload = collect($validated)->except('lawyers')->toArray();
        if (empty($casePayload['lawyer_id'])) {
            $casePayload['lawyer_id'] = $firstLawyerId;
        }

        // Get old lawyers to recalculate stats
        $oldLawyers = $case->lawyers()->pluck('lawyers.id')->toArray();

        $original = $case->getOriginal();
        $case->update($casePayload);
        $changes = $case->getChanges();

        // Sync lawyers
        $syncData = [];
        foreach ($lawyersData as $lData) {
            $syncData[$lData['id']] = ['outcome' => $lData['outcome']];
        }
        $case->lawyers()->sync($syncData);

        // Recalculate stats for all affected lawyers (union of old and new lawyers)
        $affectedLawyers = array_unique(array_merge($oldLawyers, array_keys($syncData)));
        foreach ($affectedLawyers as $lawyerId) {
            $lawyer = Lawyer::find($lawyerId);
            if ($lawyer) {
                $lawyer->recalculateStatistics();
            }
        }

        if (!empty($changes)) {
            AuditLog::create([
                'user_id' => auth()->id(),
                'user_name' => auth()->user()->name,
                'lawyer_id' => $case->lawyer_id,
                'lawyer_name' => $case->lawyer?->name ?? 'Unknown',
                'action' => 'update_case',
                'details' => json_encode([
                    'old' => array_intersect_key($original, $changes),
                    'new' => $changes,
                ]),
            ]);
        }

        return redirect()->route('admin.cases.index')->with('success', 'Case record updated successfully.');
    }

    /**
     * Remove the specified case from storage.
     */
    public function caseDestroy(string $id): RedirectResponse
    {
        // Enforce role permissions: only ghost and simp can delete cases
        if (!in_array(auth()->user()->system, ['ghost', 'simp'])) {
            abort(403, 'Only the Admin or Site Owner is authorized to delete cases.');
        }

        $case = LawyerCase::findOrFail($id);

        // Find attached lawyers to recalculate stats before deletion
        $attachedLawyerIds = $case->lawyers()->pluck('lawyers.id')->toArray();

        AuditLog::create([
            'user_id' => auth()->id(),
            'user_name' => auth()->user()->name,
            'lawyer_id' => $case->lawyer_id,
            'lawyer_name' => $case->lawyer?->name ?? 'Unknown',
            'action' => 'delete_case',
            'details' => json_encode(['fields' => $case->toArray()]),
        ]);

        $case->delete();

        // Recalculate stats for formerly attached lawyers
        foreach ($attachedLawyerIds as $lawyerId) {
            $lawyer = Lawyer::find($lawyerId);
            if ($lawyer) {
                $lawyer->recalculateStatistics();
            }
        }

        return redirect()->route('admin.cases.index')->with('success', 'Case record deleted successfully.');
    }
}
