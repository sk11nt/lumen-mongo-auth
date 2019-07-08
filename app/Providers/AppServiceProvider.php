<?php

namespace App\Providers;

use App\Contracts\MailService as MailServiceInterface;
use App\Contracts\PasswordEncoder;
use App\Contracts\QueueService;
use App\Services\MailService;
use App\Services\MongoQueue;
use App\Services\PasswordHashEncoder;
use App\Services\Registration;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('user.registration', Registration::class);
        $this->app->bind(PasswordEncoder::class, PasswordHashEncoder::class);
        $this->app->bind(QueueService::class, MongoQueue::class);
        $this->app->bind(MailServiceInterface::class, MailService::class);
    }
}
