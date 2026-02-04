<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WelcomeUserNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        private string $verificationToken,
        private string $temporaryPassword
    ) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        // Use env() directly instead of config()
        $verificationUrl = env('FRONTEND_URL', 'http://localhost:4200') . '/verify-email?token=' . $this->verificationToken;

        return (new MailMessage)
            ->subject('Welcome - Verify Your Account')
            ->greeting('Hello ' . $notifiable->email . '!')
            ->line('An account has been created for you by an administrator.')
            ->line('**Temporary Password:** ' . $this->temporaryPassword)
            ->line('For security reasons, you must verify your email and set a new password before you can access your account.')
            ->action('Verify Email & Set Password', $verificationUrl)
            ->line('This verification link will expire in 48 hours.')
            ->line('If you did not expect this account creation, please contact support immediately.')
            ->salutation('Best regards, ' . config('app.name'));
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
