<?php

namespace App\Providers;

use TCG\Voyager\Facades\Voyager;
use App\FormFields\OptionsFormField;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\URL;

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
        Voyager::addFormField(OptionsFormField::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
        Schema::defaultStringLength(191);

        $this->app->bind('path.public', function() {
            return base_path().'/public_html';
        });

        /*VerifyEmail::toMailUsing(function ($notifiable) {
            $verifyUrl = URL::temporarySignedRoute(
                'verification.verify', Carbon::now()->addMinutes(60), ['id' => $notifiable->getKey()]
            );

            return (new MailMessage)
                ->subject('Email verification by Selectinteriorworld !')
                ->markdown('emails.verify', ['url' => $verifyUrl]);
        });*/        

        /*$this->app->bind('path.storage', function () {
            return base_path().'/../public_html/storage';
        });*/
    }
}
