<?php
namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class SlaBreachedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public string $entityType,     // 'requisition'
        public int $entityId,
        public string $stepRole,       // role that owns the step
        public int $hoursLate,         // hours beyond SLA
        public ?string $extra = null
    ) {}

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject("SLA Breach: {$this->entityType} #{$this->entityId}")
            ->greeting('Heads up 👋')
            ->line("A {$this->entityType} approval is past its SLA.")
            ->line("Entity ID: #{$this->entityId}")
            ->line("Step role: {$this->stepRole}")
            ->line("Late by: {$this->hoursLate} hour(s)")
            ->action('Open in HMS', url("/requisitions/{$this->entityId}/edit"))
            ->line($this->extra ?? '');
    }

    public function toArray($notifiable)
    {
        return [
            'entity_type' => $this->entityType,
            'entity_id' => $this->entityId,
            'step_role' => $this->stepRole,
            'hours_late' => $this->hoursLate,
            'extra' => $this->extra,
        ];
    }
}
