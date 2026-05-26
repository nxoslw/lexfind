<?php

use App\Models\User;
use App\Models\Lawyer;
use App\Models\LawyerCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('guest cannot manage lawyer profiles', function () {
    $this->get('/admin/lawyers')->assertRedirect(route('login'));
    $this->post('/admin/lawyers', [])->assertRedirect(route('login'));
});

test('non-admin user cannot access admin lawyer management routes', function () {
    $user = User::factory()->create(['system' => 'god']);
    $this->actingAs($user);

    $this->get('/admin/lawyers')->assertForbidden();
    $this->post('/admin/lawyers', [])->assertForbidden();
});

test('admin can create a lawyer profile with nested cases, peer reviews, and trial style details', function () {
    $admin = User::factory()->create(['system' => 'simp']);
    $this->actingAs($admin);

    $lawyerData = [
        'name' => 'John Doe, Esq.',
        'title' => 'Managing Partner',
        'firm' => 'Doe & Associates',
        'city' => 'Miami',
        'state' => 'FL',
        'specialty' => 'Corporate Fraud',
        'bio' => 'Experienced business litigator.',
        'avatar_color' => '#1E3A54',
        'initials' => 'JD',
        'email' => 'john@doe.legal',
        'phone' => '+1 (305) 555-0199',
        'website' => 'doe.legal',
        'linkedin' => 'https://linkedin.com/in/johndoe',
        'years_experience' => 15,
        'cases_count' => 100,
        'cases_won' => 90,
        'cases_lost' => 10,
        'cases_settled' => 0,
        'cases_active' => 5,
        'financial_recovery' => '$10M+',
        'fee_structure' => 'Hourly',
        'is_certified' => true,
        'rating' => 4.9,
        'availability' => 'available',
        'user_id' => null,
        'practice_areas' => ['Corporate Law', 'Business Litigation'],
        'criminal_record' => 'CLEARED',
        'bar_discipline' => 'CLEARED',
        'trial_style' => 'Detail-oriented.',
        'peer_reviews' => [
            'rating' => '4.9 / 5.0',
            'source' => 'Martindale-Hubbell',
            'quote' => 'Doe is highly strategic.',
            'author' => 'Chambers Panel',
        ],
        'trial_style_details' => [
            'approach' => 'Surgical.',
            'forensics' => 'Data-backed.',
            'global' => 'Excellent.',
        ],
        'recent_activity' => [],
        'cases' => [
            [
                'name' => 'Acme Corp v. Beta LLC',
                'case_number' => '2026-CA-00123',
                'jurisdiction' => 'Florida State Court',
                'court' => 'Circuit Court of Miami-Dade',
                'type' => 'civil',
                'type_label' => 'Commercial Litigation',
                'status' => 'decided',
                'year' => 2026,
                'won_party' => 'Plaintiff',
                'motions_count' => 3,
                'motion_success_rate' => '100%',
                'summary' => 'Corporate separation dispute.',
                'key_finding' => 'Contract was binding.',
            ]
        ]
    ];

    $response = $this->post('/admin/lawyers', $lawyerData);
    $response->assertRedirect(route('admin.lawyers.index'));

    $this->assertDatabaseHas('lawyers', [
        'name' => 'John Doe, Esq.',
        'email' => 'john@doe.legal',
    ]);

    $lawyer = Lawyer::where('email', 'john@doe.legal')->first();
    expect($lawyer->peer_reviews['rating'])->toBe('4.9 / 5.0');
    expect($lawyer->trial_style_details['approach'])->toBe('Surgical.');

    $this->assertDatabaseHas('lawyer_cases', [
        'lawyer_id' => $lawyer->id,
        'name' => 'Acme Corp v. Beta LLC',
    ]);
});

test('admin can update a lawyer profile and sync cases', function () {
    $admin = User::factory()->create(['system' => 'simp']);
    $this->actingAs($admin);

    $lawyer = Lawyer::create([
        'name' => 'Original Name',
        'title' => 'Partner',
        'firm' => 'Firm',
        'city' => 'Miami',
        'state' => 'FL',
        'specialty' => 'IP',
        'bio' => 'Original bio.',
        'avatar_color' => '#000000',
        'initials' => 'ON',
        'email' => 'original@email.com',
        'phone' => '123',
        'website' => 'site.com',
        'years_experience' => 5,
        'cases_count' => 10,
        'cases_won' => 9,
        'cases_lost' => 1,
        'cases_settled' => 0,
        'cases_active' => 0,
        'is_certified' => false,
        'rating' => 4.0,
        'availability' => 'available',
        'practice_areas' => ['IP'],
        'criminal_record' => 'CLEARED',
        'bar_discipline' => 'CLEARED',
    ]);

    $caseToKeep = LawyerCase::create([
        'lawyer_id' => $lawyer->id,
        'name' => 'Case To Keep',
        'case_number' => '123',
        'jurisdiction' => 'FL',
        'court' => 'Court',
        'type' => 'civil',
        'type_label' => 'Civil',
        'status' => 'decided',
        'year' => 2024,
    ]);
    $lawyer->cases()->attach($caseToKeep, ['outcome' => 'won']);

    $caseToDelete = LawyerCase::create([
        'lawyer_id' => $lawyer->id,
        'name' => 'Case To Delete',
        'case_number' => '456',
        'jurisdiction' => 'FL',
        'court' => 'Court',
        'type' => 'civil',
        'type_label' => 'Civil',
        'status' => 'decided',
        'year' => 2024,
    ]);
    $lawyer->cases()->attach($caseToDelete, ['outcome' => 'lost']);

    $updateData = [
        'name' => 'Updated Name',
        'title' => 'Partner',
        'firm' => 'Firm',
        'city' => 'Miami',
        'state' => 'FL',
        'specialty' => 'IP',
        'bio' => 'Original bio.',
        'avatar_color' => '#000000',
        'initials' => 'ON',
        'email' => 'original@email.com',
        'phone' => '123',
        'website' => 'site.com',
        'years_experience' => 5,
        'cases_count' => 10,
        'cases_won' => 9,
        'cases_lost' => 1,
        'cases_settled' => 0,
        'cases_active' => 0,
        'is_certified' => false,
        'rating' => 4.0,
        'availability' => 'available',
        'practice_areas' => ['IP'],
        'criminal_record' => 'CLEARED',
        'bar_discipline' => 'CLEARED',
        'cases' => [
            [
                'id' => $caseToKeep->id,
                'name' => 'Case To Keep Updated',
                'case_number' => '123',
                'jurisdiction' => 'FL',
                'court' => 'Court',
                'type' => 'civil',
                'type_label' => 'Civil',
                'status' => 'decided',
                'year' => 2024,
            ],
            [
                'name' => 'Newly Added Case',
                'case_number' => '789',
                'jurisdiction' => 'FL',
                'court' => 'Court',
                'type' => 'civil',
                'type_label' => 'Civil',
                'status' => 'decided',
                'year' => 2025,
            ]
        ]
    ];

    $response = $this->post("/admin/lawyers/{$lawyer->id}", $updateData);
    $response->assertRedirect(route('admin.lawyers.index'));

    $this->assertDatabaseHas('lawyers', [
        'id' => $lawyer->id,
        'name' => 'Updated Name',
    ]);

    $this->assertDatabaseHas('lawyer_cases', [
        'id' => $caseToKeep->id,
        'name' => 'Case To Keep Updated',
    ]);

    $this->assertDatabaseMissing('lawyer_cases', [
        'id' => $caseToDelete->id,
    ]);

    $this->assertDatabaseHas('lawyer_cases', [
        'lawyer_id' => $lawyer->id,
        'name' => 'Newly Added Case',
    ]);
});

test('attorney can view and update their assigned lawyer profile', function () {
    $attorney = User::factory()->create(['system' => 'god']);
    $this->actingAs($attorney);

    $lawyer = Lawyer::create([
        'user_id' => $attorney->id,
        'name' => 'Attorneys Profile',
        'title' => 'Partner',
        'firm' => 'Firm',
        'city' => 'Miami',
        'state' => 'FL',
        'specialty' => 'Arbitration',
        'bio' => 'Original bio.',
        'avatar_color' => '#000000',
        'initials' => 'AP',
        'email' => 'original@email.com',
        'phone' => '123',
        'website' => 'site.com',
        'years_experience' => 5,
        'cases_count' => 10,
        'cases_won' => 9,
        'cases_lost' => 1,
        'cases_settled' => 0,
        'cases_active' => 0,
        'is_certified' => false,
        'rating' => 4.0,
        'availability' => 'available',
        'practice_areas' => ['Arbitration'],
        'criminal_record' => 'CLEARED',
        'bar_discipline' => 'CLEARED',
    ]);

    // View edit page
    $this->get(route('lawyer.edit', $lawyer->slug))->assertOk();

    // Submit update
    $updatePayload = [
        'bio' => 'Updated bio information.',
        'email' => 'new-email@doe.com',
        'phone' => '999-999-9999',
        'website' => 'doe-new.com',
        'linkedin' => 'https://linkedin.com/in/updated',
        'availability' => 'busy',
    ];

    $response = $this->post(route('lawyer.update', $lawyer->slug), $updatePayload);
    $response->assertRedirect(route('lawyer.edit', $lawyer->slug));

    $this->assertDatabaseHas('lawyers', [
        'id' => $lawyer->id,
        'bio' => 'Updated bio information.',
        'email' => 'new-email@doe.com',
        'availability' => 'busy',
    ]);
});

test('attorney cannot update another attorneys profile', function () {
    $attorney1 = User::factory()->create(['system' => 'god']);
    $attorney2 = User::factory()->create(['system' => 'god']);

    $lawyer = Lawyer::create([
        'user_id' => $attorney2->id,
        'name' => 'Attorneys Profile',
        'title' => 'Partner',
        'firm' => 'Firm',
        'city' => 'Miami',
        'state' => 'FL',
        'specialty' => 'Arbitration',
        'bio' => 'Original bio.',
        'avatar_color' => '#000000',
        'initials' => 'AP',
        'email' => 'original@email.com',
        'phone' => '123',
        'website' => 'site.com',
        'years_experience' => 5,
        'cases_count' => 10,
        'cases_won' => 9,
        'cases_lost' => 1,
        'cases_settled' => 0,
        'cases_active' => 0,
        'is_certified' => false,
        'rating' => 4.0,
        'availability' => 'available',
        'practice_areas' => ['Arbitration'],
        'criminal_record' => 'CLEARED',
        'bar_discipline' => 'CLEARED',
    ]);

    $this->actingAs($attorney1);

    // Try to view edit page
    $this->get(route('lawyer.edit', $lawyer->slug))->assertForbidden();

    // Try to update
    $this->post(route('lawyer.update', $lawyer->slug), [
        'bio' => 'Hack attempt.',
        'email' => 'hacker@hack.com',
        'phone' => '000',
        'website' => 'hack.com',
        'availability' => 'available',
    ])->assertForbidden();
});

test('user with assigned profile can access profile root page', function () {
    $user = User::factory()->create(['system' => 'god']);
    $lawyer = Lawyer::create([
        'user_id' => $user->id,
        'name' => 'Assigned Profile',
        'title' => 'Associate',
        'firm' => 'Firm',
        'city' => 'Miami',
        'state' => 'FL',
        'specialty' => 'Corporate',
        'bio' => 'Bio',
        'avatar_color' => '#000000',
        'initials' => 'AP',
        'email' => 'lawyer@email.com',
        'phone' => '123',
        'website' => 'site.com',
        'years_experience' => 3,
        'cases_count' => 10,
        'cases_won' => 8,
        'cases_lost' => 2,
        'cases_settled' => 0,
        'cases_active' => 0,
        'is_certified' => false,
        'rating' => 4.5,
        'availability' => 'available',
        'practice_areas' => ['Corporate'],
        'criminal_record' => 'CLEARED',
        'bar_discipline' => 'CLEARED',
    ]);

    $this->actingAs($user);
    $this->get(route('lawyer.profile-root'))->assertOk();
});

test('front mod without assigned profile can access profile root page', function () {
    $moderator = User::factory()->create(['system' => 'bip']);

    $this->actingAs($moderator);
    $this->get(route('lawyer.profile-root'))->assertOk();
});

test('standard user without assigned profile cannot access profile root page', function () {
    $user = User::factory()->create(['system' => 'god']);

    $this->actingAs($user);
    $this->get(route('lawyer.profile-root'))->assertForbidden();
});
