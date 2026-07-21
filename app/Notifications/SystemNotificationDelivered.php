<?php

namespace App\Notifications;

use App\Models\SystemNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class SystemNotificationDelivered extends Notification
{
    use Queueable;

    public function __construct(private readonly SystemNotification $systemNotification)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => $this->systemNotification->title,
            'message' => $this->systemNotification->content,
            'type' => $this->systemNotification->type,
            'system_notification_id' => $this->systemNotification->id,
        ];
    }
}