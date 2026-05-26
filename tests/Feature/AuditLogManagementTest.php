<?php

use App\Models\User;
use App\Models\AuditLog;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('guests are redirected to the login page', function () {
    $this->get('/admin/audit-logs')->assertRedirect(route('login'));
});

test('unprivileged users cannot access audit logs page', function () {
    $user = User::factory()->create(['system' => 'god']);
    $this->actingAs($user);

    $this->get('/admin/audit-logs')->assertForbidden();
});

test('site owners (simp) and admins (ghost) can access audit logs page', function () {
    $simp = User::factory()->create(['system' => 'simp']);
    $ghost = User::factory()->create(['system' => 'ghost']);

    $this->actingAs($simp);
    $this->get('/admin/audit-logs')->assertOk();

    $this->actingAs($ghost);
    $this->get('/admin/audit-logs')->assertOk();
});

test('can search audit logs by operator name, action, or subject lawyer', function () {
    $admin = User::factory()->create(['system' => 'ghost']);
    $this->actingAs($admin);

    AuditLog::create([
        'user_id' => $admin->id,
        'user_name' => 'Alice Operator',
        'lawyer_id' => 1,
        'lawyer_name' => 'Lawyer Bob',
        'action' => 'assign',
        'details' => '{}',
    ]);

    AuditLog::create([
        'user_id' => $admin->id,
        'user_name' => 'Charlie Operator',
        'lawyer_id' => 2,
        'lawyer_name' => 'Lawyer David',
        'action' => 'delete',
        'details' => '{}',
    ]);

    // Search by operator
    $response = $this->get('/admin/audit-logs?search=Alice');
    $response->assertOk();
    $data = $response->original->getData()['page']['props']['auditLogs']['data'];
    expect($data)->toHaveCount(1);
    expect($data[0]['user_name'])->toBe('Alice Operator');

    // Search by action
    $response = $this->get('/admin/audit-logs?search=delete');
    $response->assertOk();
    $data = $response->original->getData()['page']['props']['auditLogs']['data'];
    expect($data)->toHaveCount(1);
    expect($data[0]['action'])->toBe('delete');

    // Search by subject lawyer
    $response = $this->get('/admin/audit-logs?search=David');
    $response->assertOk();
    $data = $response->original->getData()['page']['props']['auditLogs']['data'];
    expect($data)->toHaveCount(1);
    expect($data[0]['lawyer_name'])->toBe('Lawyer David');
});

test('can filter audit logs by time period', function () {
    $admin = User::factory()->create(['system' => 'ghost']);
    $this->actingAs($admin);

    // Create a log for today
    AuditLog::create([
        'user_id' => $admin->id,
        'user_name' => 'Operator',
        'lawyer_id' => 1,
        'lawyer_name' => 'Lawyer One',
        'action' => 'create',
        'details' => '{}',
        'created_at' => now(),
    ]);

    // Create a log for 10 days ago
    $logOld = AuditLog::create([
        'user_id' => $admin->id,
        'user_name' => 'Operator',
        'lawyer_id' => 2,
        'lawyer_name' => 'Lawyer Two',
        'action' => 'update',
        'details' => '{}',
    ]);
    $logOld->created_at = now()->subDays(10);
    $logOld->save();

    // Filter by today
    $response = $this->get('/admin/audit-logs?time=today');
    $response->assertOk();
    $data = $response->original->getData()['page']['props']['auditLogs']['data'];
    expect($data)->toHaveCount(1);
    expect($data[0]['lawyer_name'])->toBe('Lawyer One');

    // Filter by last 15 days
    $response = $this->get('/admin/audit-logs?time=15_days');
    $response->assertOk();
    $data = $response->original->getData()['page']['props']['auditLogs']['data'];
    expect($data)->toHaveCount(2);

    // Filter by last 3 days
    $response = $this->get('/admin/audit-logs?time=3_days');
    $response->assertOk();
    $data = $response->original->getData()['page']['props']['auditLogs']['data'];
    expect($data)->toHaveCount(1);
    expect($data[0]['lawyer_name'])->toBe('Lawyer One');
});

test('can configure per_page rows count and paginate audit logs', function () {
    $admin = User::factory()->create(['system' => 'ghost']);
    $this->actingAs($admin);

    // Create 30 logs
    for ($i = 1; $i <= 30; $i++) {
        AuditLog::create([
            'user_id' => $admin->id,
            'user_name' => 'Operator',
            'lawyer_id' => 1,
            'lawyer_name' => "Lawyer $i",
            'action' => 'create',
            'details' => '{}',
        ]);
    }

    // Default per_page should be 25
    $response = $this->get('/admin/audit-logs');
    $response->assertOk();
    $pageData = $response->original->getData()['page']['props']['auditLogs'];
    expect($pageData['data'])->toHaveCount(25);
    expect($pageData['total'])->toBe(30);

    // Set per_page to 50
    $response = $this->get('/admin/audit-logs?per_page=50');
    $response->assertOk();
    $pageData = $response->original->getData()['page']['props']['auditLogs'];
    expect($pageData['data'])->toHaveCount(30);

    // Set per_page to 25 and check page 2
    $response = $this->get('/admin/audit-logs?per_page=25&page=2');
    $response->assertOk();
    $pageData = $response->original->getData()['page']['props']['auditLogs'];
    expect($pageData['data'])->toHaveCount(5);
});

test('admin dashboard only shows last 10 audit logs', function () {
    $admin = User::factory()->create(['system' => 'ghost']);
    $this->actingAs($admin);

    // Create 15 logs
    for ($i = 1; $i <= 15; $i++) {
        AuditLog::create([
            'user_id' => $admin->id,
            'user_name' => 'Operator',
            'lawyer_id' => 1,
            'lawyer_name' => "Lawyer $i",
            'action' => 'create',
            'details' => '{}',
        ]);
    }

    $response = $this->get('/admin/dashboard');
    $response->assertOk();
    $logs = $response->original->getData()['page']['props']['auditLogs'];
    expect($logs)->toHaveCount(10);
});
