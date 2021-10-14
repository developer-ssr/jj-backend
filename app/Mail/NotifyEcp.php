<?php

namespace App\Mail;

use App\Models\Email;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NotifyEcp extends Mailable
{
    use Queueable, SerializesModels;

    public  $email;

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
        return $this->markdown('emails.ecp.notify')
                    ->attachFromStorage($this->email->path, [
                        'as' => $this->email->file,
                        'mime' => 'application/pdf'
                    ])
                    ->subject($this->email->subject)
                    ->with([
                        'message' => $this->email->message
                    ]);
    }
}
