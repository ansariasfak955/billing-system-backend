<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ForgetPassword extends Mailable
{
    use Queueable, SerializesModels;
    public $token;
    public $company_name;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($token, $company_name = null)
    {
        $this->token = $token;
        $this->company_name = $company_name;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $this->markdown('emails.forget-password');

        $this->withSwiftMessage(function ($message) {
            $message->getHeaders()->addTextHeader(
                'IsTransactional', 'true'
            );
        });

        return $this;
    }
}
