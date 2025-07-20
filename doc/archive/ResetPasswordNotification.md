namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class ResetPasswordNotification extends Notification
{
    public string $token;

    public function __construct($token)
    {
        $this->token = $token;
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $resetUrl = url("/reset-password?token={$this->token}&email=" . urlencode($notifiable->email));

        return (new MailMessage)
            ->line('Click the button below to reset your password:')
            ->action('Reset Password', $resetUrl)
            ->line('If you didn’t request a password reset, no action is needed.');
    }
}
