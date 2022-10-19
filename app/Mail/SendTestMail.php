<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendTestMail extends Mailable
{
    use Queueable, SerializesModels;
    public $configuration, $recipient, $company, $settings;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($configuration, $recipient, $company, $settings)
    {
        $this->configuration = $configuration;
        $this->recipient = $recipient;
        $this->settings = $settings;
        $this->company = $company;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $this->from($this->configuration['from_email'], $this->configuration['from_name'])
            ->cc($this->configuration['send_copy_to'])
            ->replyTo($this->configuration['reply_to'])
            ->markdown('emails.send-test-mail');
        $this->withSwiftMessage(function ($message) {
            $message->getHeaders()->addTextHeader(
                'IsTransactional', 'true'
            );
        });
        return $this;
    }
}