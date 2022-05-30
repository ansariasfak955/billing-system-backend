<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendTestMail extends Mailable
{
    use Queueable, SerializesModels;
    public $configuration, $recipient, $company, $email;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($configuration, $recipient, $company, $email)
    {
        $this->configuration = $configuration;
        $this->recipient = $recipient;
        $this->email = $email;
        $this->company = $company;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $this->from('example@example.com', 'Example')->markdown('emails.send-test-mail');
        $this->withSwiftMessage(function ($message) {
            $message->getHeaders()->addTextHeader(
                'IsTransactional', 'true'
            );
        });
        return $this;
    }
}