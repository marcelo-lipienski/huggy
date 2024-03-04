<?php

namespace App\Providers;

use App\Infrastructure\Interfaces\Api\ContactInterface;
use App\Infrastructure\RdStation\Contact;
use GuzzleHttp\Client;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;

class CrmServiceProvider extends ServiceProvider implements DeferrableProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(ContactInterface::class, function (Application $app) {
            return new Contact(
                new Client()
            );
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }

    /** @return array<int, string> */
    public function provides(): array
    {
        return [ContactInterface::class];
    }
}
