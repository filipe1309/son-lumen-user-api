<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AccountCreated extends Notification
{
    /**
     * @var User
     */
    private User $user;

    /**
     * @var
     */
    private $redirect;

    /**
     * AccountCreated contructor.
     *
     * @param User $user
     */
    public function __construct(User $user, $redirect)
    {
        $this->user = $user;
        $this->redirect = $redirect;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage())
            ->subject('Your account has been created')
            ->greeting("Hi {$this->user->name},")
            ->line('Your account has been created')
            ->action('Access this address to validade', $this->redirect)
            ->line('Thanks for using our app')
            ->salutation('Att,');
    }
}
