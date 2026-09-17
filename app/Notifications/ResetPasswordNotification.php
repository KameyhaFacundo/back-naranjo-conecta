<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Reemplaza la notificación de reseteo de Laravel: el link apunta al
 * frontend (que es el que tiene la pantalla para cargar la nueva
 * contraseña), no a una vista Blade que esta API no tiene.
 */
class ResetPasswordNotification extends Notification
{
    public function __construct(private string $token)
    {
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $url = rtrim(config('app.frontend_url'), '/').'/restablecer-password?token='.$this->token
            .'&email='.urlencode($notifiable->getEmailForPasswordReset());

        $saludo = $notifiable->nombre ? 'Hola, '.$notifiable->nombre.'!' : 'Hola!';

        return (new MailMessage)
            ->subject('Restablecer tu contraseña — El Naranjo Conecta')
            ->greeting($saludo)
            ->line('Recibimos un pedido para restablecer la contraseña de tu cuenta.')
            ->action('Restablecer contraseña', $url)
            ->line('Este link vence en 60 minutos.')
            ->line('Si vos no pediste esto, podés ignorar este mensaje: tu contraseña sigue igual.')
            ->salutation('— El Naranjo Conecta');
    }
}
