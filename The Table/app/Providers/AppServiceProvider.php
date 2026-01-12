<?php

declare(strict_types=1);

namespace App\Providers;

use App\Contracts\CohortServiceInterface;
use App\Models\Cohort;
use App\Models\User;
use App\Policies\CohortPolicy;
use App\Policies\UserPolicy;
use App\Services\CohortService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * All of the container bindings that should be registered.
     *
     * @var array<class-string, class-string>
     */
    public array $bindings = [
        CohortServiceInterface::class => CohortService::class,
    ];

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
        // Fix for older MySQL versions
        Schema::defaultStringLength(191);

        // Set asset URL for subfolder installations
        $appUrl = config('app.url');
        if ($appUrl && (str_contains($appUrl, 'The round table') || str_contains($appUrl, 'The%20round%20table'))) {
            \Illuminate\Support\Facades\URL::forceRootUrl($appUrl);
        }

        // Register Policies for Authorization
        Gate::policy(Cohort::class, CohortPolicy::class);
        Gate::policy(User::class, UserPolicy::class);

        // Enable strict mode in local/development
        if ($this->app->environment('local', 'testing')) {
            Model::shouldBeStrict();
        }

        // In production, just prevent lazy loading (N+1 queries)
        if ($this->app->environment('production')) {
            Model::preventLazyLoading();
        }
    }
}
