<?php

namespace App\Notifications;

use App\UserRegistration;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Lang;

class UserRegisteredNotification extends Notification
{
    use Queueable;

    /**
     * @var UserRegistration
     */
    private $applicant;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(UserRegistration $applicant)
    {
        $this->applicant = $applicant;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     *
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     *
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $actionUrl = url(config('app.url').'/admin/users/create');

        return (new MailMessage)
            ->greeting(__('Hello Admin'))
            ->line(__(':firstName :lastName (:email) from :city just applied for a login.', [
                'firstName' => $this->applicant->getFirstName(),
                'lastName'  => $this->applicant->getLastName(),
                'email'     => $this->applicant->getEmail(),
                'city'      => $this->applicant->getCity()
            ]))
            ->line(__('Verify, that this person is permitted to have an account, before creating it.'))
            ->action('Create Account', $actionUrl)
            ->line(__(':firstName left the following comment: :comment', [
                'firstName' => $this->applicant->getFirstName(),
                'comment'   => $this->applicant->getComment(),
            ]))
            ->line('Have an excellent day :)');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     *
     * @return array
     */
    public function toArray($notifiable)
    {
        return (array) $this->applicant;
    }
}
