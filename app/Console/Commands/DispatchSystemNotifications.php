<?php

namespace App\Console\Commands;

use App\Models\ActivityLog;
use App\Models\SystemNotification;
use App\Models\User;
use App\Notifications\SystemNotificationDelivered;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;

class DispatchSystemNotifications extends Command
{
    protected $signature = 'notifications:dispatch-system';

    protected $description = 'Dispara notificações de sistema agendadas para os usuários-alvo.';

    public function handle(): int
    {
        $now = Carbon::now();

        $notifications = SystemNotification::query()
            ->where('status', 'active')
            ->where('type', 'push')
            ->whereNull('sent_at')
            ->where('start_at', '<=', $now)
            ->where('end_at', '>=', $now)
            ->get();

        if ($notifications->isEmpty()) {
            $this->info('No system push notifications to dispatch.');
            return self::SUCCESS;
        }

        $dispatchedCount = 0;

        foreach ($notifications as $notification) {
            $recipients = $this->resolveRecipients($notification->send_to);

            if ($recipients->isEmpty()) {
                continue;
            }

            Notification::send($recipients, new SystemNotificationDelivered($notification));

            $notification->update(['sent_at' => $now]);

            ActivityLog::writeLog(
                'Notificação',
                'PUSH',
                "Disparou a notificação push #{$notification->id} para {$recipients->count()} usuário(s): {$notification->title}"
            );

            $dispatchedCount++;
        }

        $this->info("Dispatched {$dispatchedCount} system push notification(s).");

        return self::SUCCESS;
    }

    private function resolveRecipients(string $sendTo)
    {
        return match ($sendTo) {
            'all' => User::query()->get(),
            'atendente' => User::query()->where('role', 'atendente')->get(),
            'user' => User::query()->where('role', 'user')->get(),
            default => collect(),
        };
    }
}