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
}
