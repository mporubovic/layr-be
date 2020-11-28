<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InviteCreated extends Mailable
{
    use Queueable, SerializesModels;

    public $token;
    public $tutor;
    public $student;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($token, $tutor, $student)
    {
        $this->token = $token;
        $this->tutor = $tutor;
        $this->student = $student;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {

        return $this->markdown('emails.invite')->subject($this->tutor->name . ' invited you to join their class on MyLayr.com');
    }
}
