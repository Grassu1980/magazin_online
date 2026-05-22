<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Configure mail settings dynamically from database
        try {
            $mailConfig = getMailConfig();

            if ($mailConfig) {
                Config::set('mail.mailers.smtp.host', $mailConfig['host']);
                Config::set('mail.mailers.smtp.port', $mailConfig['port']);
                Config::set('mail.mailers.smtp.encryption', $mailConfig['encryption']);
                Config::set('mail.mailers.smtp.username', $mailConfig['username']);
                Config::set('mail.mailers.smtp.password', $mailConfig['password']);
                Config::set('mail.from.address', $mailConfig['from']['address']);
                Config::set('mail.from.name', $mailConfig['from']['name']);
            }
        } catch (\Exception $e) {
            // If database is not available or settings are not configured, use default config
        }
    }
}