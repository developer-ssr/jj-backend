<?php

namespace App\Mail;

use App\Models\Email;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class JnJMail extends Mailable
{
    use Queueable, SerializesModels;

    public  $email;
    public $attachment;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Email $email)
    {
        $this->email = $email;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $email = $this->email;
        return $this->markdown('emails.jnj')
                    ->attachFromStorage($this->email->path)
                    ->subject($email->subject);
    }
}