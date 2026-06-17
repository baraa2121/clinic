<?php

namespace App\Providers;

use App\Models\Admin;
use App\Policies\AdminPolicy;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Laravel\Passport\Passport;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
        Schema::defaultStringLength(191);
        Passport::enablePasswordGrant();
        Passport::tokensExpireIn(Carbon::now()->addMinutes(15));    
    }
}
