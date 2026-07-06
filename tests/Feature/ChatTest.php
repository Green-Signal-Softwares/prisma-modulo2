<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Solicitation;
use App\Models\Message;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ChatTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $agent;
    protected $solicitation;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware([
            \Illuminate\Foundation\Http\Middleware\PreventRequestForgery::class,
        ]);

        $this->user = User::factory()->create(['role' => 'user']);
        $this->agent = User::factory()->create(['role' => 'atendente']);
        
        $this->solicitation = Solicitation::create([
            'user_id' => $this->user->id,
            'title' => 'Minha Solicitação',
            'description' => 'Descrição detalhada da solicitação',
            'status' => 'em_atendimento',
            'ticket_number' => 'T-100'
        ]);
    }

    public function test_user_can_send_chat_message()
    {
        $response = $this->actingAs($this->user)
            ->postJson(route('chat.messages.store', $this->solicitation), [
                'text' => 'Olá atendente, preciso de ajuda.'
            ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'message' => [
                'id', 'text', 'sender', 'sender_id', 'is_user', 'time', 'reactions', 'read_at'
            ]
        ]);

        $this->assertDatabaseHas('messages', [
            'solicitation_id' => $this->solicitation->id,
            'user_id' => $this->user->id,
            'text' => 'Olá atendente, preciso de ajuda.'
        ]);
    }

    public function test_user_can_get_chat_updates()
    {
        // Cria uma mensagem existente
        $msg = Message::create([
            'solicitation_id' => $this->solicitation->id,
            'user_id' => $this->agent->id,
            'text' => 'Olá, em que posso ajudar?'
        ]);

        $response = $this->actingAs($this->user)
            ->getJson(route('chat.messages.updates', [
                'solicitation' => $this->solicitation->id,
                'last_id' => 0
            ]));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'new_messages',
            'updated_states',
            'solicitation_status'
        ]);

        $this->assertCount(1, $response->json('new_messages'));
    }

    public function test_user_can_toggle_reaction()
    {
        $msg = Message::create([
            'solicitation_id' => $this->solicitation->id,
            'user_id' => $this->agent->id,
            'text' => 'Olá, em que posso ajudar?'
        ]);

        // Adiciona reação 👍
        $response = $this->actingAs($this->user)
            ->postJson(route('chat.messages.react', $msg), [
                'emoji' => '👍'
            ]);

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'success' => true
        ]);

        // Verifica que o banco de dados foi atualizado
        $msg->refresh();
        $this->assertArrayHasKey('👍', $msg->reactions);
        $this->assertContains($this->user->id, $msg->reactions['👍']);

        // Remove a reação 👍 ao enviar novamente
        $response2 = $this->actingAs($this->user)
            ->postJson(route('chat.messages.react', $msg), [
                'emoji' => '👍'
            ]);

        $response2->assertStatus(200);
        $msg->refresh();
        $this->assertEmpty($msg->reactions);
    }

    public function test_user_can_edit_own_message()
    {
        $msg = Message::create([
            'solicitation_id' => $this->solicitation->id,
            'user_id' => $this->user->id,
            'text' => 'Mensagem original'
        ]);

        $response = $this->actingAs($this->user)
            ->putJson(route('chat.messages.edit', $msg), [
                'text' => 'Mensagem editada'
            ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'text' => 'Mensagem editada'
        ]);

        $this->assertDatabaseHas('messages', [
            'id' => $msg->id,
            'text' => 'Mensagem editada'
        ]);
    }

    public function test_user_cannot_edit_other_user_message()
    {
        $msg = Message::create([
            'solicitation_id' => $this->solicitation->id,
            'user_id' => $this->agent->id,
            'text' => 'Mensagem do atendente'
        ]);

        $response = $this->actingAs($this->user)
            ->putJson(route('chat.messages.edit', $msg), [
                'text' => 'Tentativa de edição'
            ]);

        $response->assertStatus(403);
    }

    public function test_user_can_delete_own_message()
    {
        $msg = Message::create([
            'solicitation_id' => $this->solicitation->id,
            'user_id' => $this->user->id,
            'text' => 'Mensagem para apagar'
        ]);

        $response = $this->actingAs($this->user)
            ->deleteJson(route('chat.messages.delete', $msg));

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true
        ]);

        $this->assertDatabaseMissing('messages', [
            'id' => $msg->id
        ]);
    }

    public function test_agent_can_initiate_attendance()
    {
        // Cria um chamado em estado de fila para o atendente puxar
        $queued = Solicitation::create([
            'user_id' => $this->user->id,
            'title' => 'Chamado na fila',
            'description' => 'Aguardando atendimento',
            'status' => 'na_fila',
            'ticket_number' => 'T-200'
        ]);

        $response = $this->actingAs($this->agent)
            ->postJson(route('atendente.solicitations.iniciar', $queued));

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true
        ]);

        $queued->refresh();
        $this->assertEquals('em_atendimento', $queued->status);
        $this->assertEquals($this->agent->id, $queued->atendente_id);
    }

    public function test_user_cannot_initiate_attendance()
    {
        $response = $this->actingAs($this->user)
            ->post(route('atendente.solicitations.iniciar', $this->solicitation));

        $response->assertStatus(302);
        $response->assertRedirect(route('dashboard'));
    }

    public function test_cannot_initiate_videocall_when_solicitation_in_queue()
    {
        // Cria um chamado em estado de fila
        $queued = Solicitation::create([
            'user_id' => $this->user->id,
            'title' => 'Chamado na fila',
            'description' => 'Aguardando atendimento',
            'status' => 'na_fila',
            'ticket_number' => 'T-200'
        ]);

        // Tenta iniciar chamada de vídeo por Jitsi (initiateCall)
        $response = $this->actingAs($this->user)
            ->postJson(route('videocall.initiate'), [
                'solicitation_id' => $queued->id
            ]);

        $response->assertStatus(422);
        $response->assertJsonFragment([
            'error' => 'Não é possível iniciar chamada de vídeo antes do atendimento ser iniciado pelo atendente.'
        ]);

        // Tenta iniciar chamada de vídeo por Google Meet redirect (redirectToGoogle)
        $response2 = $this->actingAs($this->user)
            ->get(route('google.redirect', [
                'solicitation_id' => $queued->id
            ]));

        $response2->assertStatus(302);
        $response2->assertSessionHas('error', 'Não é possível iniciar chamada de vídeo antes do atendimento ser iniciado pelo atendente.');
    }

    public function test_cannot_join_videocall_when_solicitation_in_queue()
    {
        // Cria um chamado em estado de fila
        $queued = Solicitation::create([
            'user_id' => $this->user->id,
            'title' => 'Chamado na fila',
            'description' => 'Aguardando atendimento',
            'status' => 'na_fila',
            'ticket_number' => 'T-200'
        ]);

        // Cria uma mensagem de videochamada associada a esse chamado na fila (simulando desvio)
        $message = Message::create([
            'solicitation_id' => $queued->id,
            'user_id' => $this->user->id,
            'text' => null,
            'type' => 'videocall',
            'metadata' => [
                'room_id' => 'prisma-test',
                'meet_url' => 'https://meet.google.com/abc-defg-hij',
                'status' => 'active'
            ]
        ]);

        // Tenta ingressar na chamada
        $response = $this->actingAs($this->user)
            ->get(route('videocall.join', $message));

        $response->assertStatus(302);
        $response->assertSessionHas('error', 'Não é possível acessar a chamada de vídeo antes do atendimento ser iniciado pelo atendente.');
    }

    public function test_user_can_reply_to_message()
    {
        // Cria mensagem original
        $original = Message::create([
            'solicitation_id' => $this->solicitation->id,
            'user_id' => $this->agent->id,
            'text' => 'Olá, tudo bem?'
        ]);

        // Envia resposta marcando a original
        $response = $this->actingAs($this->user)
            ->postJson(route('chat.messages.store', $this->solicitation), [
                'text' => 'Tudo ótimo!',
                'parent_id' => $original->id
            ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'message' => [
                'id', 'text', 'parent' => ['id', 'text', 'sender']
            ]
        ]);
        
        $this->assertEquals($original->id, $response->json('message.parent.id'));
    }

    public function test_reply_to_message_when_parent_has_no_user()
    {
        // Desabilita chaves estrangeiras de forma portável
        \Illuminate\Support\Facades\Schema::disableForeignKeyConstraints();

        // Cria mensagem original com user_id inexistente (ex: 999)
        $original = Message::create([
            'solicitation_id' => $this->solicitation->id,
            'user_id' => 999,
            'text' => 'Mensagem órfã'
        ]);

        // Reabilita
        \Illuminate\Support\Facades\Schema::enableForeignKeyConstraints();

        // Envia resposta marcando a original
        $response = $this->actingAs($this->user)
            ->postJson(route('chat.messages.store', $this->solicitation), [
                'text' => 'Respondendo órfã',
                'parent_id' => $original->id
            ]);

        // Deve retornar 200 JSON e o sender como SISTEMA
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'sender' => 'SISTEMA'
        ]);
    }

    public function test_agent_can_view_ticket_show_page()
    {
        $response = $this->actingAs($this->agent)
            ->get(route('atendente.tickets.show', $this->solicitation));

        $response->assertStatus(200);
        $response->assertSee('Ticket #' . $this->solicitation->ticket_number);
        $response->assertSee('Histórico completo');
        $response->assertSee('Central de mensagens');
    }

    public function test_user_cannot_view_ticket_show_page()
    {
        $response = $this->actingAs($this->user)
            ->get(route('atendente.tickets.show', $this->solicitation));

        $response->assertStatus(302); // redirected by role middleware
    }

    public function test_cannot_initiate_videocall_when_another_videocall_is_active()
    {
        // 1. Inicia uma chamada de vídeo primeiro
        $firstCallMsg = Message::create([
            'solicitation_id' => $this->solicitation->id,
            'user_id' => $this->agent->id,
            'text' => null,
            'type' => 'videocall',
            'metadata' => [
                'room_id' => 'prisma-test-1',
                'meet_url' => 'https://meet.jit.si/prisma-test-1',
                'status' => 'active'
            ]
        ]);

        // 2. Tenta iniciar outra chamada de vídeo por Jitsi (initiateCall)
        $response = $this->actingAs($this->agent)
            ->postJson(route('videocall.initiate'), [
                'solicitation_id' => $this->solicitation->id
            ]);

        $response->assertStatus(422);
        $response->assertJsonFragment([
            'error' => 'Não é possível iniciar uma nova chamada enquanto a chamada atual estiver ativa.'
        ]);

        // 3. Tenta iniciar outra chamada de vídeo por Google Meet redirect (redirectToGoogle)
        $response2 = $this->actingAs($this->agent)
            ->get(route('google.redirect', [
                'solicitation_id' => $this->solicitation->id
            ]));

        $response2->assertStatus(302);
        $response2->assertSessionHas('error', 'Não é possível iniciar uma nova chamada enquanto a chamada atual estiver ativa.');
    }

    public function test_can_initiate_videocall_when_all_previous_videocalls_ended()
    {
        // 1. Inicia uma chamada de vídeo anterior
        $firstCallMsg = Message::create([
            'solicitation_id' => $this->solicitation->id,
            'user_id' => $this->agent->id,
            'text' => null,
            'type' => 'videocall',
            'metadata' => [
                'room_id' => 'prisma-test-1',
                'meet_url' => 'https://meet.jit.si/prisma-test-1',
                'status' => 'ended'
            ]
        ]);

        // 2. Tenta iniciar nova chamada de vídeo por Jitsi (initiateCall)
        $response = $this->actingAs($this->agent)
            ->postJson(route('videocall.initiate'), [
                'solicitation_id' => $this->solicitation->id
            ]);

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'success' => true
        ]);
    }

    public function test_joincall_generates_and_appends_jitsi_jwt_token_when_configured()
    {
        // 1. Configura Jitsi com App ID e Secret fictícios
        config()->set('services.jitsi.domain', 'jitsi.myhost.com');
        config()->set('services.jitsi.app_id', 'test_app_id');
        config()->set('services.jitsi.secret', 'test_secret_key');

        // 2. Cria mensagem de videochamada ativa
        $callMsg = Message::create([
            'solicitation_id' => $this->solicitation->id,
            'user_id' => $this->agent->id,
            'text' => null,
            'type' => 'videocall',
            'metadata' => [
                'room_id' => 'prisma-test-room',
                'meet_url' => 'https://jitsi.myhost.com/prisma-test-room',
                'status' => 'active'
            ]
        ]);

        // 3. Executa a requisição JSON de joinCall
        $response = $this->actingAs($this->agent)
            ->getJson(route('videocall.join', $callMsg));

        $response->assertStatus(200);
        $url = $response->json('url');
        
        $this->assertStringContainsString('https://jitsi.myhost.com/prisma-test-room', $url);
        $this->assertStringContainsString('jwt=', $url);

        // O token JWT deve conter a assinatura correta e informações do usuário
        $jwtToken = explode('jwt=', $url)[1];
        $this->assertNotEmpty($jwtToken);
    }
}

