<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Solicitation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RbacTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_login()
    {
        $response = $this->get(route('dashboard'));
        $response->assertRedirect(route('login'));

        $response2 = $this->get(route('atendente.dashboard'));
        $response2->assertRedirect(route('login'));
    }

    public function test_client_can_access_client_dashboard_and_is_redirected_from_agent_dashboard()
    {
        $client = User::factory()->create(['role' => 'user']);

        $response = $this->actingAs($client)->get(route('dashboard'));
        $response->assertOk();

        $response2 = $this->actingAs($client)->get(route('atendente.dashboard'));
        $response2->assertRedirect(route('dashboard'));
    }

    public function test_agent_can_access_agent_dashboard_and_is_redirected_from_client_dashboard()
    {
        $agent = User::factory()->create(['role' => 'atendente']);

        $response = $this->actingAs($agent)->get(route('atendente.dashboard'));
        $response->assertOk();

        $response2 = $this->actingAs($agent)->get(route('dashboard'));
        $response2->assertRedirect(route('atendente.dashboard'));
    }

    public function test_client_only_sees_their_own_solicitations_on_dashboard()
    {
        $client = User::factory()->create(['role' => 'user']);
        $otherClient = User::factory()->create(['role' => 'user']);

        Solicitation::create([
            'user_id' => $client->id,
            'title' => 'Cliente Sol 1',
            'description' => 'Desc 1',
            'status' => 'aberta',
            'ticket_number' => '123'
        ]);

        Solicitation::create([
            'user_id' => $otherClient->id,
            'title' => 'Outro Cliente Sol',
            'description' => 'Desc 2',
            'status' => 'aberta',
            'ticket_number' => '456'
        ]);

        $response = $this->actingAs($client)->get(route('dashboard'));
        $response->assertSee('Cliente Sol 1');
        $response->assertDontSee('Outro Cliente Sol');
    }

    public function test_agent_sees_all_solicitations_on_dashboard()
    {
        $agent = User::factory()->create(['role' => 'atendente']);
        $client1 = User::factory()->create(['role' => 'user']);
        $client2 = User::factory()->create(['role' => 'user']);

        Solicitation::create([
            'user_id' => $client1->id,
            'title' => 'Sol Cliente 1',
            'description' => 'Desc 1',
            'status' => 'aberta',
            'ticket_number' => '123'
        ]);

        Solicitation::create([
            'user_id' => $client2->id,
            'title' => 'Sol Cliente 2',
            'description' => 'Desc 2',
            'status' => 'aberta',
            'ticket_number' => '456'
        ]);

        $response = $this->actingAs($agent)->get(route('atendente.dashboard'));
        $response->assertSee('Sol Cliente 1');
        $response->assertSee('Sol Cliente 2');
    }

    public function test_agent_can_access_historico_and_client_is_redirected()
    {
        $agent = User::factory()->create(['role' => 'atendente']);
        $client = User::factory()->create(['role' => 'user']);

        // Guest redirect
        $this->get(route('atendente.historico'))->assertRedirect(route('login'));

        // Client redirect
        $this->actingAs($client)->get(route('atendente.historico'))->assertRedirect(route('dashboard'));

        // Agent access
        $this->actingAs($agent)->get(route('atendente.historico'))->assertOk();
    }

    public function test_admin_rbac_redirections_and_access()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $agent = User::factory()->create(['role' => 'atendente']);
        $client = User::factory()->create(['role' => 'user']);

        // 1. Guests are redirected
        $this->get(route('admin.dashboard'))->assertRedirect(route('login'));

        // 2. Client and agent are redirected to their respective dashboards when attempting to access admin dashboard
        $this->actingAs($client)->get(route('admin.dashboard'))->assertRedirect(route('dashboard'));
        $this->actingAs($agent)->get(route('admin.dashboard'))->assertRedirect(route('atendente.dashboard'));

        // 3. Admin can access admin dashboard and see all solicitations
        Solicitation::create([
            'user_id' => $client->id,
            'title' => 'Admin Test Sol',
            'description' => 'Admin Desc',
            'status' => 'aberta',
            'ticket_number' => '999'
        ]);
        $response = $this->actingAs($admin)->get(route('admin.dashboard'));
        $response->assertOk();
        $response->assertSee('Admin Test Sol');

        // 4. Admin redirected from other dashboards to admin dashboard
        $this->actingAs($admin)->get(route('dashboard'))->assertRedirect(route('admin.dashboard'));
        $this->actingAs($admin)->get(route('atendente.dashboard'))->assertRedirect(route('admin.dashboard'));

        // 5. Admin can access other admin pages, while agent and client cannot
        $adminPages = [
            'admin.gestao-atendimento',
            'admin.presets-globais',
            'admin.log-atividades'
        ];

        foreach ($adminPages as $page) {
            $this->actingAs($admin)->get(route($page))->assertOk();
            $this->actingAs($client)->get(route($page))->assertRedirect(route('dashboard'));
            $this->actingAs($agent)->get(route($page))->assertRedirect(route('atendente.dashboard'));
        }
    }
}
