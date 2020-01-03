<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Spatie\WelcomeNotification\WelcomeNotification;

class AccountCreatedNotification extends WelcomeNotification
{
    public function buildWelcomeNotificationMessage(): MailMessage
    {
        return (new MailMessage)
            ->greeting(__('Hello :firstName', ['firstName' => $this->user->first_name]))
            ->line(__("We've just created you an account for :app. It's a super simple tool to create images in the corporate design of the GREENS.",
                ['app' => config('app.name')]))
            ->line(__('Set yourself a password, and off you go.'))
            ->action('Set Password', $this->showWelcomeFormUrl)
            ->line('Have an excellent day :)');
    }
}
