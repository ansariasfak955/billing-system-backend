<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Mail\ForgetPassword;
use Mail;

class SendEmailForgetPassword implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $token;
    public $email;
    public $company_name;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($token, $email, $company_name = null)
    {
        $this->token = $token;
        $this->email = $email;
        $this->company_name = $company_name;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Mail::to($this->email)->send(new ForgetPassword($this->token, $this->company_name));
    }
}
