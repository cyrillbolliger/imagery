<?php

namespace App\Mail;

use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Auth;

class AccountCreated extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The receiver
     *
     * @var User $user
     */
    public $user;

    /**
     * The sender
     *
     * @var User $manger
     */
    public $manager;

    /**
     * Create a new message instance.
     *
     * @param User $user
     *
     * @return void
     */
    public function __construct(User $user)
    {
        $this->user = $user;
        $this->manager = Auth::getUser();
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->subject(__('Welcome to :app', ['app' => config('app.name')]))
            ->replyTo($this->manager->email)
            ->markdown('emails.account-created');
    }
}
