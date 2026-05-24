<?php

namespace App\Notifications\Auth;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class VerifyEmailNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * The verification token.
     *
     * @var string
     */
    protected string $token;

    /**
     * The user's email.
     *
     * @var string
     */
    protected string $email;

    /**
     * Create a new notification instance.
     */
    public function __construct(string $token, string $email)
    {
        $this->token = $token;
        $this->email = $email;
    }

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
        $verificationUrl = env('FRONTEND_URL', config('app.frontend_url')) . '/verify-email?token=' . $this->token . '&email=' . urlencode($this->email);

        return (new MailMessage)
            ->subject('Verifikasi Email Anda - ' . config('app.name'))
            ->view('emails.verify-email', [
                'notifiable' => $notifiable,
                'verificationUrl' => $verificationUrl,
                'token' => $this->token,
            ]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'token' => $this->token,
            'email' => $this->email,
        ];
    }
}
