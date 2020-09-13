<?php

namespace App\Mail;

use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PendingApproval extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The user that waits for approval
     *
     * @var User
     */
    public $applicant;

    /**
     * The keycloak groups the applicant belongs to
     *
     * @var array
     */
    public $groups;

    /**
     * Create a new message instance.
     *
     * @param User $applicant the user that waits for approval
     * @param array $groups the keycloak groups the applicant is in
     */
    public function __construct(User $applicant, array $groups)
    {
        $this->applicant = $applicant;
        $this->groups = $groups;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->subject(__('Pending approval for :app', ['app' => config('app.name')]))
            ->replyTo($this->applicant->email)
            ->markdown('emails.pending-approval');
    }
}
