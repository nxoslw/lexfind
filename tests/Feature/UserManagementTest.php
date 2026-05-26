<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('guests are redirected to the login page for users registry', function () {
    $this->get('/admin/users')->assertRedirect(route('login'));
});

test('unprivileged users cannot access users registry page', function (string $role) {
    $user = User::factory()->create(['system' => $role]);
    $this->actingAs($user);

    $this->get('/admin/users')->assertForbidden();
})->with(['god', 'bip']);

test('site owners (simp), admins (ghost), and mods (bat) can access users registry page', function (string $role) {
    $user = User::factory()->create(['system' => $role]);
    $this->actingAs($user);

    $this->get('/admin/users')->assertOk();
})->with(['simp', 'ghost', 'bat']);

test('site owner (simp) cannot see ghost admin accounts in the users registry list', function () {
    $simp = User::factory()->create(['system' => 'simp']);
    $ghost = User::factory()->create(['system' => 'ghost', 'name' => 'Ghost Admin']);
    $god = User::factory()->create(['system' => 'god', 'name' => 'Standard User']);

    $this->actingAs($simp);

    $response = $this->get('/admin/users');
    $response->assertOk();

    $users = $response->original->getData()['page']['props']['users'];

    // Verify ghost is not in list, but god and simp are
    $userNames = collect($users)->pluck('name')->toArray();
    expect($userNames)->toContain($god->name);
    expect($userNames)->toContain($simp->name);
    expect($userNames)->not->toContain($ghost->name);
});

test('admin (ghost) can see all accounts including ghost admin accounts', function () {
    $ghostOperator = User::factory()->create(['system' => 'ghost']);
    $ghostUser = User::factory()->create(['system' => 'ghost', 'name' => 'Another Ghost']);
    $god = User::factory()->create(['system' => 'god', 'name' => 'Standard User']);

    $this->actingAs($ghostOperator);

    $response = $this->get('/admin/users');
    $response->assertOk();

    $users = $response->original->getData()['page']['props']['users'];
    $userNames = collect($users)->pluck('name')->toArray();
    expect($userNames)->toContain($god->name);
    expect($userNames)->toContain($ghostUser->name);
    expect($userNames)->toContain($ghostOperator->name);
});

test('site owner (simp) can view standard user details but cannot view ghost details', function () {
    $simp = User::factory()->create(['system' => 'simp']);
    $god = User::factory()->create(['system' => 'god']);
    $ghost = User::factory()->create(['system' => 'ghost']);

    $this->actingAs($simp);

    // Can view standard user
    $this->get("/admin/users/{$god->id}")->assertOk();

    // Cannot view ghost admin
    $this->get("/admin/users/{$ghost->id}")->assertForbidden();
});

test('site owner (simp) can create standard user but cannot create ghost admin', function () {
    $simp = User::factory()->create(['system' => 'simp']);
    $this->actingAs($simp);

    // Can create god user
    $response = $this->post('/admin/users', [
        'name' => 'New God User',
        'email' => 'newgod@example.com',
        'password' => 'password123',
        'system' => 'god',
    ]);
    $response->assertRedirect(route('admin.users.index'));
    $this->assertDatabaseHas('users', ['email' => 'newgod@example.com', 'system' => 'god']);

    // Cannot create ghost user
    $response2 = $this->post('/admin/users', [
        'name' => 'New Ghost User',
        'email' => 'newghost@example.com',
        'password' => 'password123',
        'system' => 'ghost',
    ]);
    $response2->assertSessionHasErrors(['system']);
    $this->assertDatabaseMissing('users', ['email' => 'newghost@example.com']);
});

test('site owner (simp) can update standard user but cannot update ghost admin', function () {
    $simp = User::factory()->create(['system' => 'simp']);
    $god = User::factory()->create(['system' => 'god']);
    $ghost = User::factory()->create(['system' => 'ghost']);

    $this->actingAs($simp);

    // Can update god user
    $response = $this->post("/admin/users/{$god->id}", [
        'name' => 'Updated God User',
        'email' => $god->email,
        'system' => 'bip',
        'banned' => null,
    ]);
    $response->assertRedirect(route('admin.users.index'));
    $this->assertDatabaseHas('users', ['id' => $god->id, 'name' => 'Updated God User', 'system' => 'bip']);

    // Cannot update ghost user
    $response2 = $this->post("/admin/users/{$ghost->id}", [
        'name' => 'Updated Ghost User',
        'email' => $ghost->email,
        'system' => 'ghost',
        'banned' => null,
    ]);
    $response2->assertForbidden();

    // Cannot change user role to ghost
    $response3 = $this->post("/admin/users/{$god->id}", [
        'name' => 'Updated God User',
        'email' => $god->email,
        'system' => 'ghost',
        'banned' => null,
    ]);
    $response3->assertSessionHasErrors(['system']);
});

test('site owner (simp) can delete standard user but cannot delete ghost admin', function () {
    $simp = User::factory()->create(['system' => 'simp']);
    $god = User::factory()->create(['system' => 'god']);
    $ghost = User::factory()->create(['system' => 'ghost']);

    $this->actingAs($simp);

    // Cannot delete ghost admin
    $this->post("/admin/users/{$ghost->id}/delete")->assertForbidden();
    $this->assertDatabaseHas('users', ['id' => $ghost->id]);

    // Can delete standard user
    $this->post("/admin/users/{$god->id}/delete")->assertRedirect(route('admin.users.index'));
    $this->assertDatabaseMissing('users', ['id' => $god->id]);
});

test('site owner (simp) can reset standard user password but cannot reset ghost admin password', function () {
    $simp = User::factory()->create(['system' => 'simp']);
    $god = User::factory()->create(['system' => 'god']);
    $ghost = User::factory()->create(['system' => 'ghost']);

    $this->actingAs($simp);

    // Can reset standard user password
    $this->post("/admin/users/{$god->id}/reset-password", [
        'password' => 'newpassword123',
        'password_confirmation' => 'newpassword123',
    ])->assertRedirect(route('admin.users.show', $god->id));

    // Cannot reset ghost admin password
    $this->post("/admin/users/{$ghost->id}/reset-password", [
        'password' => 'newpassword123',
        'password_confirmation' => 'newpassword123',
    ])->assertForbidden();
});

test('dashboard stats counts exclude ghost admins for site owners (simp)', function () {
    $simp = User::factory()->create(['system' => 'simp']);
    $ghost = User::factory()->create(['system' => 'ghost']);
    $god1 = User::factory()->create(['system' => 'god']);
    $god2 = User::factory()->create(['system' => 'god', 'banned' => 'Reason']);

    // Log in as admin (ghost) - should see all users
    $this->actingAs($ghost);
    $responseAdmin = $this->get('/admin/dashboard');
    $responseAdmin->assertOk();
    $statsAdmin = $responseAdmin->original->getData()['page']['props']['stats'];
    expect($statsAdmin['total_users'])->toBe(4);
    expect($statsAdmin['privileged_users'])->toBe(2); // ghost + simp
    expect($statsAdmin['banned_users'])->toBe(1);

    // Log in as site owner (simp) - should exclude ghost
    $this->actingAs($simp);
    $responseSimp = $this->get('/admin/dashboard');
    $responseSimp->assertOk();
    $statsSimp = $responseSimp->original->getData()['page']['props']['stats'];
    expect($statsSimp['total_users'])->toBe(3); // excludes ghost
    expect($statsSimp['privileged_users'])->toBe(1); // simp only (excludes ghost)
    expect($statsSimp['banned_users'])->toBe(1);
});

test('mod (bat) cannot see ghost or simp accounts in the users registry list', function () {
    $bat = User::factory()->create(['system' => 'bat']);
    $simp = User::factory()->create(['system' => 'simp', 'name' => 'Site Owner']);
    $ghost = User::factory()->create(['system' => 'ghost', 'name' => 'Ghost Admin']);
    $god = User::factory()->create(['system' => 'god', 'name' => 'Standard User']);

    $this->actingAs($bat);

    $response = $this->get('/admin/users');
    $response->assertOk();

    $users = $response->original->getData()['page']['props']['users'];
    $userNames = collect($users)->pluck('name')->toArray();
    expect($userNames)->toContain($god->name);
    expect($userNames)->toContain($bat->name);
    expect($userNames)->not->toContain($simp->name);
    expect($userNames)->not->toContain($ghost->name);
});

test('mod (bat) can view standard user details but cannot view admin details', function (string $adminRole) {
    $bat = User::factory()->create(['system' => 'bat']);
    $god = User::factory()->create(['system' => 'god']);
    $admin = User::factory()->create(['system' => $adminRole]);

    $this->actingAs($bat);

    // Can view standard user
    $this->get("/admin/users/{$god->id}")->assertOk();

    // Cannot view admin
    $this->get("/admin/users/{$admin->id}")->assertForbidden();
})->with(['ghost', 'simp']);

test('mod (bat) can create standard user but cannot create admin roles', function (string $adminRole) {
    $bat = User::factory()->create(['system' => 'bat']);
    $this->actingAs($bat);

    // Can create god user
    $response = $this->post('/admin/users', [
        'name' => 'New User',
        'email' => 'newuser@example.com',
        'password' => 'password123',
        'system' => 'god',
    ]);
    $response->assertRedirect(route('admin.users.index'));

    // Cannot create admin
    $response2 = $this->post('/admin/users', [
        'name' => 'New Admin',
        'email' => 'newadmin@example.com',
        'password' => 'password123',
        'system' => $adminRole,
    ]);
    $response2->assertSessionHasErrors(['system']);
})->with(['ghost', 'simp']);

test('mod (bat) can update standard user but cannot update admin users or change user role to admin', function (string $adminRole) {
    $bat = User::factory()->create(['system' => 'bat']);
    $god = User::factory()->create(['system' => 'god']);
    $admin = User::factory()->create(['system' => $adminRole]);

    $this->actingAs($bat);

    // Can update god user
    $response = $this->post("/admin/users/{$god->id}", [
        'name' => 'Updated God User',
        'email' => $god->email,
        'system' => 'bip',
        'banned' => null,
    ]);
    $response->assertRedirect(route('admin.users.index'));

    // Cannot update admin
    $response2 = $this->post("/admin/users/{$admin->id}", [
        'name' => 'Updated Admin',
        'email' => $admin->email,
        'system' => $adminRole,
        'banned' => null,
    ]);
    $response2->assertForbidden();

    // Cannot change user role to admin
    $response3 = $this->post("/admin/users/{$god->id}", [
        'name' => 'Updated God User',
        'email' => $god->email,
        'system' => $adminRole,
        'banned' => null,
    ]);
    $response3->assertSessionHasErrors(['system']);
})->with(['ghost', 'simp']);

test('mod (bat) can delete standard user but cannot delete admin users', function (string $adminRole) {
    $bat = User::factory()->create(['system' => 'bat']);
    $god = User::factory()->create(['system' => 'god']);
    $admin = User::factory()->create(['system' => $adminRole]);

    $this->actingAs($bat);

    // Cannot delete admin
    $this->post("/admin/users/{$admin->id}/delete")->assertForbidden();

    // Can delete standard user
    $this->post("/admin/users/{$god->id}/delete")->assertRedirect(route('admin.users.index'));
})->with(['ghost', 'simp']);

test('mod (bat) can reset standard user password but cannot reset admin password', function (string $adminRole) {
    $bat = User::factory()->create(['system' => 'bat']);
    $god = User::factory()->create(['system' => 'god']);
    $admin = User::factory()->create(['system' => $adminRole]);

    $this->actingAs($bat);

    // Can reset standard user password
    $this->post("/admin/users/{$god->id}/reset-password", [
        'password' => 'newpassword123',
        'password_confirmation' => 'newpassword123',
    ])->assertRedirect(route('admin.users.show', $god->id));

    // Cannot reset admin password
    $this->post("/admin/users/{$admin->id}/reset-password", [
        'password' => 'newpassword123',
        'password_confirmation' => 'newpassword123',
    ])->assertForbidden();
})->with(['ghost', 'simp']);

test('dashboard stats counts exclude admins (ghost and simp) for mods (bat)', function () {
    $bat = User::factory()->create(['system' => 'bat']);
    $simp = User::factory()->create(['system' => 'simp']);
    $ghost = User::factory()->create(['system' => 'ghost']);
    $god = User::factory()->create(['system' => 'god']);

    $this->actingAs($bat);
    $response = $this->get('/admin/dashboard');
    $response->assertOk();
    $stats = $response->original->getData()['page']['props']['stats'];
    expect($stats['total_users'])->toBe(2); // bat + god
    expect($stats['privileged_users'])->toBe(1); // bat only (excludes ghost and simp)
});
