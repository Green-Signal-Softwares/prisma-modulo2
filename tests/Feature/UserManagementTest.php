<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\AccessProfile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    private $admin;
    private $agent;
    private $client;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->agent = User::factory()->create(['role' => 'atendente']);
        $this->client = User::factory()->create(['role' => 'user']);
    }

    public function test_only_admin_can_access_users_management_page()
    {
        $this->get(route('users.index'))->assertRedirect(route('login'));

        $this->actingAs($this->client)
            ->get(route('users.index'))
            ->assertRedirect(route('dashboard'));

        $this->actingAs($this->agent)
            ->get(route('users.index'))
            ->assertRedirect(route('atendente.dashboard'));

        $this->actingAs($this->admin)
            ->get(route('users.index'))
            ->assertOk()
            ->assertViewHasAll(['profiles', 'users']);
    }

    public function test_only_admin_can_access_gestao_atendimento_page()
    {
        $this->get(route('admin.gestao-atendimento'))->assertRedirect(route('login'));

        $this->actingAs($this->client)
            ->get(route('admin.gestao-atendimento'))
            ->assertRedirect(route('dashboard'));

        $this->actingAs($this->agent)
            ->get(route('admin.gestao-atendimento'))
            ->assertRedirect(route('atendente.dashboard'));

        $this->actingAs($this->admin)
            ->get(route('admin.gestao-atendimento'))
            ->assertOk();
    }

    public function test_admin_can_create_access_profile()
    {
        $response = $this->actingAs($this->admin)
            ->postJson(route('admin.access-profiles.store'), [
                'name' => 'ATENDENTE TESTE N1',
                'nivel_n1' => true,
                'nivel_n2' => false,
                'fila' => true
            ]);

        $response->assertOk()
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('access_profiles', [
            'name' => 'ATENDENTE TESTE N1',
            'nivel_n1' => 1,
            'nivel_n2' => 0,
            'fila' => 1
        ]);
    }

    public function test_admin_can_update_access_profile()
    {
        $profile = AccessProfile::create([
            'name' => 'ANTIGO',
            'nivel_n1' => false,
            'nivel_n2' => false,
            'fila' => false
        ]);

        $response = $this->actingAs($this->admin)
            ->putJson(route('admin.access-profiles.update', $profile), [
                'name' => 'NOVO NOME',
                'nivel_n1' => true,
                'nivel_n2' => true,
                'fila' => true
            ]);

        $response->assertOk();
        $this->assertDatabaseHas('access_profiles', [
            'id' => $profile->id,
            'name' => 'NOVO NOME',
            'nivel_n1' => 1,
            'nivel_n2' => 1,
            'fila' => 1
        ]);
    }

    public function test_admin_can_toggle_access_profile_permissions()
    {
        $profile = AccessProfile::create([
            'name' => 'PERFIL TOGGLE',
            'nivel_n1' => false,
            'nivel_n2' => false,
            'fila' => false
        ]);

        $response = $this->actingAs($this->admin)
            ->putJson(route('admin.access-profiles.toggle', $profile), [
                'field' => 'nivel_n2',
                'value' => true
            ]);

        $response->assertOk();
        $this->assertDatabaseHas('access_profiles', [
            'id' => $profile->id,
            'nivel_n2' => 1
        ]);
    }

    public function test_admin_can_delete_access_profile()
    {
        $profile = AccessProfile::create([
            'name' => 'DELETAR',
            'nivel_n1' => true,
            'nivel_n2' => false,
            'fila' => true
        ]);

        $response = $this->actingAs($this->admin)
            ->deleteJson(route('admin.access-profiles.destroy', $profile));

        $response->assertOk();
        $this->assertDatabaseMissing('access_profiles', ['id' => $profile->id]);
    }

    public function test_admin_can_create_user()
    {
        $profile = AccessProfile::create([
            'name' => 'PERFIL USER',
            'nivel_n1' => true,
            'nivel_n2' => false,
            'fila' => true
        ]);

        $response = $this->actingAs($this->admin)
            ->postJson(route('admin.users.store'), [
                'name' => 'TESTE ATENDENTE',
                'email' => 'teste.atend@claro.com.br',
                'phone' => '(31) 98765-4321',
                'login' => 'F999999',
                'access_profile_id' => $profile->id,
                'status' => 'ativo',
                'password' => 'Senha123'
            ]);

        $response->assertOk()
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('users', [
            'name' => 'TESTE ATENDENTE',
            'email' => 'teste.atend@claro.com.br',
            'phone' => '(31) 98765-4321',
            'login' => 'F999999',
            'access_profile_id' => $profile->id,
            'status' => 'ativo',
            'role' => 'atendente'
        ]);
    }

    public function test_admin_can_update_user()
    {
        $profile1 = AccessProfile::create(['name' => 'P1']);
        $profile2 = AccessProfile::create(['name' => 'P2']);

        $user = User::factory()->create([
            'name' => 'VELHO',
            'email' => 'velho@claro.com.br',
            'phone' => '123',
            'login' => 'F123',
            'access_profile_id' => $profile1->id,
            'status' => 'ausente',
            'role' => 'atendente'
        ]);

        $response = $this->actingAs($this->admin)
            ->putJson(route('admin.users.update', $user), [
                'name' => 'NOVO',
                'email' => 'novo@claro.com.br',
                'phone' => '456',
                'login' => 'F456',
                'access_profile_id' => $profile2->id,
                'status' => 'ativo'
            ]);

        $response->assertOk();
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'NOVO',
            'email' => 'novo@claro.com.br',
            'phone' => '456',
            'login' => 'F456',
            'access_profile_id' => $profile2->id,
            'status' => 'ativo'
        ]);
    }

    public function test_admin_can_delete_user()
    {
        $user = User::factory()->create(['role' => 'atendente']);

        $response = $this->actingAs($this->admin)
            ->deleteJson(route('admin.users.destroy', $user));

        $response->assertOk();
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }

    public function test_user_can_access_profile_central()
    {
        $this->get(route('profile.central'))->assertRedirect(route('login'));

        $this->actingAs($this->client)
            ->get(route('profile.central'))
            ->assertOk()
            ->assertViewIs('profile.central');
    }

    public function test_user_can_update_profile_info()
    {
        $response = $this->actingAs($this->client)
            ->post(route('profile.update'), [
                'name' => 'Novo Nome Cliente',
                'phone' => '(99) 99999-9999',
                'login' => 'CLIENTE999',
                'email' => 'novoemail@claro.com.br'
            ]);

        $response->assertRedirect(route('profile.central'));
        $this->assertDatabaseHas('users', [
            'id' => $this->client->id,
            'name' => 'Novo Nome Cliente',
            'phone' => '(99) 99999-9999',
            'login' => 'CLIENTE999',
            'email' => 'novoemail@claro.com.br'
        ]);
    }

    public function test_user_can_update_password()
    {
        $user = User::factory()->create([
            'role' => 'user',
            'email' => 'userpasswordtest@claro.com.br',
            'password' => \Illuminate\Support\Facades\Hash::make('password123')
        ]);

        $response = $this->actingAs($user)
            ->post(route('profile.update'), [
                'name' => $user->name,
                'email' => $user->email,
                'current_password' => 'password123',
                'new_password' => 'newpassword123',
                'new_password_confirmation' => 'newpassword123'
            ]);

        $response->assertRedirect(route('profile.central'));
        $user->refresh();
        $this->assertTrue(\Illuminate\Support\Facades\Hash::check('newpassword123', $user->password));
    }

    public function test_admin_can_access_notifications_index()
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.notifications.index'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.notifications.index');
    }

    public function test_non_admin_cannot_access_notifications_index()
    {
        $response = $this->actingAs($this->client)
            ->get(route('admin.notifications.index'));

        $response->assertRedirect(route('dashboard'));
    }

    public function test_admin_can_create_system_notification()
    {
        $data = [
            'send_to' => 'all',
            'type' => 'push',
            'status' => 'inactive',
            'start_date' => '2026-03-26',
            'start_time' => '10:00',
            'end_date' => '2026-03-27',
            'end_time' => '18:00',
            'title' => 'Test Notification Title',
            'content' => 'Test notification content text here.',
        ];

        $response = $this->actingAs($this->admin)
            ->post(route('admin.notifications.store'), $data);

        $response->assertRedirect(route('admin.notifications.index'));
        $this->assertDatabaseHas('system_notifications', [
            'title' => 'Test Notification Title',
            'content' => 'Test notification content text here.',
            'send_to' => 'all',
            'type' => 'push',
            'status' => 'inactive',
        ]);
    }

    public function test_admin_can_update_system_notification()
    {
        $notification = \App\Models\SystemNotification::create([
            'send_to' => 'all',
            'type' => 'system',
            'status' => 'inactive',
            'start_at' => '2026-03-26 10:00:00',
            'end_at' => '2026-03-27 18:00:00',
            'title' => 'Original Title',
            'content' => 'Original content',
        ]);

        $updateData = [
            'send_to' => 'atendente',
            'type' => 'email',
            'status' => 'active',
            'start_date' => '2026-04-01',
            'start_time' => '12:00',
            'end_date' => '2026-04-02',
            'end_time' => '14:00',
            'title' => 'Updated Title',
            'content' => 'Updated content',
        ];

        $response = $this->actingAs($this->admin)
            ->put(route('admin.notifications.update', $notification->id), $updateData);

        $response->assertRedirect(route('admin.notifications.index'));
        $this->assertDatabaseHas('system_notifications', [
            'id' => $notification->id,
            'title' => 'Updated Title',
            'content' => 'Updated content',
            'type' => 'email',
            'send_to' => 'atendente',
            'status' => 'active',
        ]);
    }

    public function test_admin_can_delete_system_notification()
    {
        $notification = \App\Models\SystemNotification::create([
            'send_to' => 'all',
            'type' => 'system',
            'start_at' => '2026-03-26 10:00:00',
            'end_at' => '2026-03-27 18:00:00',
            'title' => 'Notification to delete',
            'content' => 'Content to delete',
        ]);

        $response = $this->actingAs($this->admin)
            ->delete(route('admin.notifications.destroy', $notification->id));

        $response->assertRedirect(route('admin.notifications.index'));
        $this->assertDatabaseMissing('system_notifications', [
            'id' => $notification->id,
        ]);
    }

    public function test_active_system_notification_renders_on_dashboard()
    {
        $notification = \App\Models\SystemNotification::create([
            'send_to' => 'all',
            'type' => 'system',
            'status' => 'active',
            'start_at' => now()->subHour(),
            'end_at' => now()->addHour(),
            'title' => 'System Title',
            'content' => 'System message text to show',
        ]);

        $response = $this->actingAs($this->admin)
            ->followingRedirects()
            ->get(route('dashboard'));

        $response->assertSee('ATENÇÃO: System message text to show', false);
    }

    public function test_active_push_notification_renders_on_dashboard()
    {
        $notification = \App\Models\SystemNotification::create([
            'send_to' => 'all',
            'type' => 'push',
            'status' => 'active',
            'start_at' => now()->subHour(),
            'end_at' => now()->addHour(),
            'title' => 'Push Title',
            'content' => 'Push message text to show in modal',
        ]);

        $response = $this->actingAs($this->admin)
            ->followingRedirects()
            ->get(route('dashboard'));

        $response->assertSee('id="push-notification-modal"', false);
        $response->assertSee('Push message text to show in modal', false);
    }

    public function test_inactive_notification_does_not_render()
    {
        $notification = \App\Models\SystemNotification::create([
            'send_to' => 'all',
            'type' => 'system',
            'status' => 'inactive',
            'start_at' => now()->subHour(),
            'end_at' => now()->addHour(),
            'title' => 'Inactive System Title',
            'content' => 'Inactive system message text',
        ]);

        $response = $this->actingAs($this->admin)
            ->followingRedirects()
            ->get(route('dashboard'));

        $response->assertDontSee('Inactive system message text', false);
    }
}
