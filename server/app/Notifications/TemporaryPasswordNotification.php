<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TemporaryPasswordNotification extends Notification
{
    use Queueable;

    public function __construct(public readonly string $temporaryPassword) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Seu acesso temporário ao Running')
            ->greeting('Olá, '.$notifiable->name.'!')
            ->line('Uma conta foi criada para você no Running.')
            ->line('Senha temporária: '.$this->temporaryPassword)
            ->line('Essa senha é de uso temporário. Você deverá criar uma nova senha assim que entrar.')
            ->action('Acessar o Running', rtrim(config('app.frontend_url'), '/').'/login')
            ->line('Se você não esperava este acesso, ignore esta mensagem e avise a academia.');
    }
}
