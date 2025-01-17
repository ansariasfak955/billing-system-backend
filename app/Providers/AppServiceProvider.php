<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Swift_SmtpTransport;
use Swift_Mailer;
use Illuminate\Mail\Mailer;
use Illuminate\Contracts\View\Factory;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('user.mailer', function ($app, $parameters) {
            $smtp_host = $parameters['smtp_host'];
            $smtp_port = $parameters['smtp_port'];
            $smtp_username = $parameters['smtp_username'];
            $smtp_password = $parameters['smtp_password'];
            $smtp_encryption = $parameters['smtp_encryption'];
             
            $from_email = $parameters['from_email'];
            $from_name = $parameters['from_name'];
             
            $transport = new Swift_SmtpTransport($smtp_host, $smtp_port);
            $transport->setUsername($smtp_username);
            $transport->setPassword($smtp_password);
            $transport->setEncryption($smtp_encryption);
             
            $swift_mailer = new Swift_Mailer($transport);
            $mailer = new Mailer('test', $app->get('view'), $swift_mailer, $app->get('events'));

            $mailer->alwaysFrom($from_email, $from_name);
            $mailer->alwaysReplyTo($from_email, $from_name);

            return $mailer;
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
