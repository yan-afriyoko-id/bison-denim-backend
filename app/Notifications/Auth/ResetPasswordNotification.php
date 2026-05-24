<?php

namespace App\Notifications\Auth;

use App\Interfaces\ConfigRepositoryInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResetPasswordNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected string $token;
    protected string $appName;

    public function __construct(string $token)
    {
        $this->token = $token;
        $configRepo = app(ConfigRepositoryInterface::class);

        $this->appName = $configRepo->getValue('app_name', config('app.name'));
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $frontendUrl = config('app.frontend_url');

        $resetUrl = $frontendUrl
            . '/reset-password?token=' . $this->token
            . '&email=' . urlencode($notifiable->email);

        return (new MailMessage)
            ->subject('Reset Password Anda - ' . $this->appName)
            ->view('emails.reset-password', [
                'user' => $notifiable,
                'resetUrl' => $resetUrl,
                'appName' => $this->appName
            ]);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'token' => $this->token,
        ];
    }
}