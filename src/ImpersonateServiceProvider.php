<?php

namespace Rickycezar\Impersonate;

use Illuminate\Auth\AuthManager;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Blade;
use Rickycezar\Impersonate\Guard\SessionGuard;
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
        $this->registerBladeDirectives();
        $this->registerMiddleware();
        $this->registerAuthDriver();
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
     * Register plugin blade directives.
     *
     * @param   void
     * @return  void
     */
    protected function registerBladeDirectives()
    {
        Blade::directive('impersonating', function () {
            return '<?php if (app()["auth"]->check() && app()["auth"]->user()->isImpersonated()): ?>';
        });

        Blade::directive('endImpersonating', function () {
            return '<?php endif; ?>';
        });

        Blade::directive('canImpersonate', function () {
            return '<?php if (app()["auth"]->check() && app()["auth"]->user()->canImpersonate()): ?>';
        });

        Blade::directive('endCanImpersonate', function () {
            return '<?php endif; ?>';
        });

        Blade::directive('canBeImpersonated', function($expression) {
            $user = trim($expression);
            return "<?php if (app()['auth']->check() && app()['auth']->user()->id != {$user}->id && {$user}->canBeImpersonated()): ?>";
        });

        Blade::directive('endCanBeImpersonated', function() {
            return '<?php endif; ?>';
        });
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

        $router->macro('impersonate', function () use ($router) {
            $router->get('/impersonate/take/{id}',
                '\Rickycezar\Impersonate\Controllers\ImpersonateController@take')->name('impersonate')->middleware('auth:api');
            $router->get('/impersonate/leave',
                '\Rickycezar\Impersonate\Controllers\ImpersonateController@leave')->name('impersonate.leave')->middleware('auth:api');
        });
    }

    /**
     * @param   void
     * @return  void
     */
    protected function registerAuthDriver()
    {
        /** @var AuthManager $auth */
        $auth = $this->app['auth'];

        $auth->extend('session', function (Application $app, $name, array $config) use ($auth) {
            $provider = $auth->createUserProvider($config['provider']);

            $guard = new SessionGuard($name, $provider, $app['session.store']);

            if (method_exists($guard, 'setCookieJar')) {
                $guard->setCookieJar($app['cookie']);
            }

            if (method_exists($guard, 'setDispatcher')) {
                $guard->setDispatcher($app['events']);
            }

            if (method_exists($guard, 'setRequest')) {
                $guard->setRequest($app->refresh('request', $guard, 'setRequest'));
            }

            return $guard;
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