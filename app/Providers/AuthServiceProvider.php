<?php

namespace App\Providers;

use App\Policies\RolePolicy;
use Carbon\Carbon;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Laravel\Passport\Passport;
use Spatie\Permission\Contracts\Role as ContractsRole;
use Spatie\Permission\Models\Role as ModelsRole;

class AuthServiceProvider extends ServiceProvider
{
    

    public function boot(): void
    {
        $this->registerPolicies();
    }
}