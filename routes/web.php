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
    // Gestão de Usuários
    Route::resource('users', UserController::class)->only(['index', 'create', 'store', 'destroy']);

    // Gestão de Notificações
    Route::get('/notifications', [DashboardController::class, 'getNotifications'])->name('notifications.index');
    Route::post('/notifications/read-all', [DashboardController::class, 'readAllNotifications'])->name('notifications.readAll');
    Route::post('/notifications/{notification}/read', [DashboardController::class, 'readNotification'])->name('notifications.read');

    // Rotas de Mensagens (Comum para Clientes e Atendentes)
    Route::post('/solicitations/{solicitation}/messages', [ChatController::class, 'storeMessage'])->name('chat.messages.store');
    Route::get('/solicitations/{solicitation}/messages/updates', [ChatController::class, 'getUpdates'])->name('chat.messages.updates');
    Route::post('/messages/{message}/react', [ChatController::class, 'toggleReaction'])->name('chat.messages.react');
    Route::put('/messages/{message}', [ChatController::class, 'editMessage'])->name('chat.messages.edit');
    Route::delete('/messages/{message}', [ChatController::class, 'deleteMessage'])->name('chat.messages.delete');

    // Videochamada
    Route::post('/videocall/initiate', [VideoCallController::class, 'initiateCall'])->name('videocall.initiate');
    Route::post('/videocall/{message}/end', [VideoCallController::class, 'endCall'])->name('videocall.end');
    Route::get('/videocall/{message}/join', [VideoCallController::class, 'joinCall'])->name('videocall.join');

    // Rotas de Cliente (Apenas role: user)
    Route::middleware(['role:user'])->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::post('/solicitations', [DashboardController::class, 'store'])->name('solicitations.store');
        Route::post('/solicitations/{solicitation}/avaliacao', [DashboardController::class, 'avaliarAtendimento'])->name('solicitations.avaliacao.store');
        Route::get('/messages/{id?}', [ChatController::class, 'index'])->name('chat.index');
    });

    // Rotas de Atendente (Apenas role: atendente)
    Route::middleware(['role:atendente'])->prefix('atendente')->name('atendente.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/messages/{id?}', [ChatController::class, 'index'])->name('chat.index');
        Route::get('/tickets', [DashboardController::class, 'tickets'])->name('tickets');
        Route::get('/historico', [DashboardController::class, 'historico'])->name('historico');
        Route::post('/solicitations/{solicitation}/iniciar', [DashboardController::class, 'iniciarAtendimento'])->name('solicitations.iniciar');
        Route::post('/solicitations/{solicitation}/finalizar', [DashboardController::class, 'finalizarAtendimento'])->name('solicitations.finalizar');
    });
});
