<?php

namespace Bets\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to your controller routes.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'Bets\Http\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        //

        parent::boot();
    }

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map()
    {
        $this->mapApiRoutes();

        $this->mapWebRoutes();

        $this->mapCustomRoutes();
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapWebRoutes()
    {
        Route::group([
            'middleware' => 'web',
            'namespace' => $this->namespace,
        ], function ($router) {
            require base_path('routes/web.php');
        });
    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapApiRoutes()
    {
        Route::group([
            'middleware' => 'api',
            'namespace' => $this->namespace,
            'prefix' => 'api',
        ], function ($router) {
            require base_path('routes/api.php');
        });
    }

    protected function mapCustomRoutes()
    {
        Route::middleware(['web', 'auth', 'check-role:admin'])
            ->prefix('admin')
            ->as('admin.')
            ->namespace("{$this->namespace}\Admin")
            ->group(base_path('routes/niveis/admin.php'));

        Route::middleware(['web', 'auth', 'check-role:supervisor', 'actives'])
            ->prefix('supervisor')
            ->as('supervisor.')
            ->namespace("{$this->namespace}\Supervisor")
            ->group(base_path('routes/niveis/supervisor.php'));

        Route::middleware(['web', 'auth', 'check-role:manager', 'actives'])
            ->prefix('manager')
            ->as('manager.')
            ->namespace("{$this->namespace}\Manager")
            ->group(base_path('routes/niveis/gerente.php'));

        Route::middleware(['web', 'auth', 'check-role:seller', 'actives'])
            ->prefix('seller')
            ->as('seller.')
            ->namespace("{$this->namespace}\Seller")
            ->group(base_path('routes/niveis/operador.php'));
    }
}
