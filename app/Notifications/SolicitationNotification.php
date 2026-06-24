<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class SolicitationNotification extends Notification
{
    use Queueable;

    protected $title;
    protected $message;
    protected $solicitationId;
    protected $type;

    /**
     * Create a new notification instance.
     */
    public function __construct(string $title, string $message, int $solicitationId, string $type)
    {
        $this->title = $title;
        $this->message = $message;
        $this->solicitationId = $solicitationId;
        $this->type = $type;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => $this->title,
            'message' => $this->message,
            'solicitation_id' => $this->solicitationId,
            'type' => $this->type,
        ];
    }
}
