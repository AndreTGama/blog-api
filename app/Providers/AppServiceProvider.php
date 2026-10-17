<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

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
        Gate::policy(\App\Models\User::class, \App\Policies\User\UserPolicy::class);
        Gate::policy(\App\Models\Category::class, \App\Policies\Category\CategoryPolicy::class);
        Gate::policy(\App\Models\Post::class, \App\Policies\Post\PostPolicy::class);
        Gate::define('viewApiDocs', fn ($user = null) => true);
    }
}
