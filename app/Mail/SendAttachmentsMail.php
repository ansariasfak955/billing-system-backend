<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendAttachmentsMail extends Mailable
{
    use Queueable, SerializesModels;
    public $attachments;
    public $subject;
    public $body;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($attachments, $subject = null, $body = null )
    {
        $this->attachments = $attachments;
        $this->subject = $subject;
        $this->body = $body;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $this->subject($this->subject)
            ->view('emails.send-attachments');
  
        foreach ($this->attachments as $file){
            $this->attach($file);
        }
  
        return $this;
    }
}
