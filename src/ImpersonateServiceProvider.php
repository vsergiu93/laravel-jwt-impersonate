<?php

namespace Rickycezar\Impersonate;

use Rickycezar\Impersonate\Middleware\ProtectFromImpersonation;
use Rickycezar\Impersonate\Services\ImpersonateManager;

/**
 * Class ServiceProvider
 *
 * @package Rickycezar\Impersonate
 */
class ImpersonateServiceProvider extends \Illuminate\Support\ServiceProvider
{
    /**
     * @var bool
     */
    protected $defer = false;

    /**
     * @var string
     */
    protected $configName = 'laravel-jwt-impersonate';

    /**
     * @var string
     */
    protected $authMiddlewareKey = 'auth_middleware';
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfig();

        $this->app->bind(ImpersonateManager::class, ImpersonateManager::class);

        $this->app->singleton(ImpersonateManager::class, function ($app) {
            return new ImpersonateManager($app);
        });

        $this->app->alias(ImpersonateManager::class, 'impersonate');

        $this->registerRoutesMacro();
        $this->registerMiddleware();
    }

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishConfig();
    }

    /**
     * Register routes macro.
     *
     * @param   void
     * @return  void
     */
    protected function registerRoutesMacro()
    {
        $router = $this->app['router'];
        $middleware = $this->configName.'.'.$this->authMiddlewareKey;

        $router->macro('impersonate', function () use ($router, $middleware) {
            $router->get('/impersonate/take/{id}',
                '\Rickycezar\Impersonate\Controllers\ImpersonateController@take')->name('impersonate')->middleware(config($middleware));
            $router->get('/impersonate/leave',
                '\Rickycezar\Impersonate\Controllers\ImpersonateController@leave')->name('impersonate.leave')->middleware(config($middleware));
            $router->get('/impersonate/info',
                '\Rickycezar\Impersonate\Controllers\ImpersonateController@info')->name('impersonate.info')->middleware(config($middleware));
        });
    }

    /**
     * Register plugin middleware.
     *
     * @param   void
     * @return  void
     */
    public function registerMiddleware()
    {
        $this->app['router']->aliasMiddleware('impersonate.protect', ProtectFromImpersonation::class);
    }

    /**
     * Merge config file.
     *
     * @param   void
     * @return  void
     */
    protected function mergeConfig()
    {
        $configPath = __DIR__ . '/../config/' . $this->configName . '.php';

        $this->mergeConfigFrom($configPath, $this->configName);
    }

    /**
     * Publish config file.
     *
     * @param   void
     * @return  void
     */
    protected function publishConfig()
    {
        $configPath = __DIR__ . '/../config/' . $this->configName . '.php';

        $this->publishes([$configPath => config_path($this->configName . '.php')], 'impersonate');
    }
}