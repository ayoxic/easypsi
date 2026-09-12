<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TeacherVerificationAdminNotification extends Notification
{
    use Queueable;

    public function __construct(private readonly User $teacher)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('EasyPsi | Nouveau professeur verifie')
            ->greeting('Nouvelle demande professeur')
            ->line('Une personne s’est inscrite comme professeur et vient de verifier son adresse email.')
            ->line('Nom : '.$this->teacher->name)
            ->line('Email : '.$this->teacher->email)
            ->line('Telephone : '.($this->teacher->phone ?: 'Non renseigne'))
            ->line('Langue choisie : '.strtoupper((string) $this->teacher->preferred_locale))
            ->line('Vous pouvez maintenant examiner ce compte dans l’administration EasyPsi.');
    }
}
