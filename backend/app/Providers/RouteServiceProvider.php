<?php

namespace App\Providers;

use Random\Engine\Secure;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\SecureApiHeaders;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        parent::boot();
    }

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map(): void
    {
        $this->mapApiRoutes();
    }

    protected function mapApiRoutes(): void
    {
        Route::prefix('api')
            ->middleware(['api', SecureApiHeaders::class])
            ->group(base_path('routes/api.php'));
    }
}
