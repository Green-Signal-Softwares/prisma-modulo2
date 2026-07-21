<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\VideoCallController;
use Illuminate\Support\Facades\Route;

// Redireciona a raiz para o dashboard (que vai pedir login caso não autenticado)
Route::get('/', function () {
    return redirect()->route('dashboard');
});

// Rotas de Autenticação (Públicas)
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Google OAuth (requer auth para garantir que o usuário já está logado)
Route::middleware(['auth'])->group(function () {
    Route::get('/google/redirect', [VideoCallController::class, 'redirectToGoogle'])->name('google.redirect');
    Route::get('/google/callback', [VideoCallController::class, 'handleGoogleCallback'])->name('google.callback');
});

// Rotas Protegidas (Requer login)
Route::middleware(['auth'])->group(function () {

    // Gestão de Notificações
    Route::get('/notifications', [DashboardController::class, 'getNotifications'])->name('notifications.index');
    Route::get('/notifications/push/active', [DashboardController::class, 'getActivePushNotification'])->name('notifications.push.active');
    Route::post('/notifications/read-all', [DashboardController::class, 'readAllNotifications'])->name('notifications.readAll');
    Route::post('/notifications/{notification}/read', [DashboardController::class, 'readNotification'])->name('notifications.read');
    Route::post('/notifications/push/{notification}/acknowledge', [DashboardController::class, 'acknowledgePushNotification'])->name('notifications.push.acknowledge');

    // Rotas de Mensagens (Comum para Clientes e Atendentes)
    Route::post('/solicitations/{solicitation}/messages', [ChatController::class, 'storeMessage'])->name('chat.messages.store');
    Route::post('/solicitations/{solicitation}/open-ticket', [ChatController::class, 'openTicket'])->name('chat.solicitations.open-ticket');
    Route::get('/solicitations/{solicitation}/messages/updates', [ChatController::class, 'getUpdates'])->name('chat.messages.updates');
    Route::post('/messages/{message}/react', [ChatController::class, 'toggleReaction'])->name('chat.messages.react');
    Route::put('/messages/{message}', [ChatController::class, 'editMessage'])->name('chat.messages.edit');
    Route::delete('/messages/{message}', [ChatController::class, 'deleteMessage'])->name('chat.messages.delete');

    // Videochamada
    Route::post('/videocall/initiate', [VideoCallController::class, 'initiateCall'])->name('videocall.initiate');
    Route::post('/videocall/{message}/end', [VideoCallController::class, 'endCall'])->name('videocall.end');
    Route::get('/videocall/{message}/join', [VideoCallController::class, 'joinCall'])->name('videocall.join');

    // Central do usuário
    Route::get('/central-usuario', [DashboardController::class, 'centralUsuario'])->name('profile.central');
    Route::post('/central-usuario/update', [DashboardController::class, 'updateProfile'])->name('profile.update');

    // Rotas de Cliente (Apenas role: user)
    Route::middleware(['role:user'])->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/solicitations/triage-flow', [DashboardController::class, 'getSupportTriageFlow'])->name('solicitations.triage-flow');
        Route::post('/solicitations', [DashboardController::class, 'store'])->name('solicitations.store');
        Route::post('/solicitations/{solicitation}/avaliacao', [DashboardController::class, 'avaliarAtendimento'])->name('solicitations.avaliacao.store');
        Route::get('/messages/{id?}', [ChatController::class, 'index'])->name('chat.index');
    });

    // Rotas de Atendente (Apenas role: atendente)
    Route::middleware(['role:atendente,admin'])->prefix('atendente')->name('atendente.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/messages/{id?}', [ChatController::class, 'index'])->name('chat.index');
        Route::get('/tickets', [DashboardController::class, 'tickets'])->name('tickets');
        Route::get('/tickets/{solicitation}', [DashboardController::class, 'showTicket'])->name('tickets.show');
        Route::get('/historico', [DashboardController::class, 'historico'])->name('historico');
        Route::post('/solicitations/{solicitation}/iniciar', [DashboardController::class, 'iniciarAtendimento'])->name('solicitations.iniciar');
        Route::post('/solicitations/{solicitation}/finalizar', [DashboardController::class, 'finalizarAtendimento'])->name('solicitations.finalizar');
    });

    Route::middleware(['role:admin'])->group(function () {
        Route::get('/gestao-usuarios', [DashboardController::class, 'gestaoUsuarios'])->name('users.index');
    });

    // Rotas de Admin (Apenas role: admin)
    Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'adminDashboard'])->name('dashboard');
        Route::get('/messages/{id?}', [ChatController::class, 'index'])->name('chat.index');
        Route::get('/tickets', [DashboardController::class, 'tickets'])->name('tickets');
        Route::get('/tickets/{solicitation}', [DashboardController::class, 'showTicket'])->name('tickets.show');
        Route::get('/historico', [DashboardController::class, 'historico'])->name('historico');
        Route::get('/gestao-atendimento', [DashboardController::class, 'gestaoAtendimento'])->name('gestao-atendimento');
        Route::post('/gestao-atendimento/salvar', [DashboardController::class, 'saveTriageFlow'])->name('gestao-atendimento.save');
        Route::get('/presets-globais', [DashboardController::class, 'presetsGlobais'])->name('presets-globais');
        Route::get('/log-atividades', [DashboardController::class, 'logAtividades'])->name('log-atividades');
        Route::get('/notifications', [DashboardController::class, 'systemNotifications'])->name('notifications.index');
        Route::post('/notifications', [DashboardController::class, 'storeSystemNotification'])->name('notifications.store');
        Route::put('/notifications/{notification}', [DashboardController::class, 'updateSystemNotification'])->name('notifications.update');
        Route::delete('/notifications/{notification}', [DashboardController::class, 'destroySystemNotification'])->name('notifications.destroy');

        Route::post('/presets', [DashboardController::class, 'storePreset'])->name('presets.store');
        Route::put('/presets/{preset}', [DashboardController::class, 'updatePreset'])->name('presets.update');
        Route::delete('/presets/{preset}', [DashboardController::class, 'destroyPreset'])->name('presets.destroy');

        Route::post('/tags', [DashboardController::class, 'storeTag'])->name('tags.store');
        Route::put('/tags/{tag}', [DashboardController::class, 'updateTag'])->name('tags.update');
        Route::delete('/tags/{tag}', [DashboardController::class, 'destroyTag'])->name('tags.destroy');

        Route::post('/access-profiles', [DashboardController::class, 'storeAccessProfile'])->name('access-profiles.store');
        Route::put('/access-profiles/{profile}', [DashboardController::class, 'updateAccessProfile'])->name('access-profiles.update');
        Route::put('/access-profiles/{profile}/toggle', [DashboardController::class, 'toggleAccessProfile'])->name('access-profiles.toggle');
        Route::delete('/access-profiles/{profile}', [DashboardController::class, 'destroyAccessProfile'])->name('access-profiles.destroy');

        Route::post('/users', [DashboardController::class, 'storeUser'])->name('users.store');
        Route::put('/users/{user}', [DashboardController::class, 'updateUser'])->name('users.update');
        Route::delete('/users/{user}', [DashboardController::class, 'destroyUser'])->name('users.destroy');
    });

    // Presets e Tags compartilhados / chat API
    Route::post('/chat/presets', [DashboardController::class, 'storeChatPreset'])->name('chat.presets.store');
    Route::put('/chat/presets/{preset}', [DashboardController::class, 'updateChatPreset'])->name('chat.presets.update');
    Route::delete('/chat/presets/{preset}', [DashboardController::class, 'destroyChatPreset'])->name('chat.presets.destroy');
    Route::post('/chat/solicitations/{solicitation}/tag', [DashboardController::class, 'updateSolicitationTag'])->name('chat.solicitations.tag');

    // Internal Notes (staff-only, gated in controller)
    Route::post('/solicitations/{solicitation}/internal-notes', [ChatController::class, 'storeInternalNote'])->name('chat.internal-notes.store');
    Route::patch('/internal-notes/{note}/pin', [ChatController::class, 'togglePinNote'])->name('chat.internal-notes.pin');
    Route::delete('/internal-notes/{note}', [ChatController::class, 'destroyInternalNote'])->name('chat.internal-notes.destroy');
});
