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
    public function __construct($files, $subject = null, $body = null )
    {
        $this->files = $files;
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
            ->markdown('emails.send-attachments');
  
        foreach ($this->files as $file){
            $this->attach($file);
        }
  
        return $this;
    }
}
