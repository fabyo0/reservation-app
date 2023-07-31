<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use App\Models\Activity;
use App\Policies\CompanyActivityPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade;
use App\Models\Company;
use App\Policies\CompanyUserPolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Company::class => CompanyUserPolicy::class,
        Activity::class => CompanyActivityPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        // Role directive
        Blade::if('role', function ($roleId) {
            return auth()->check() && auth()->user()->role_id == $roleId;
        });
    }
}
