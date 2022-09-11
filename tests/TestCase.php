<?php

namespace Rickycezar\Tests;

use Rickycezar\Impersonate\ImpersonateServiceProvider;
use Rickycezar\Tests\Stubs\Models\User;
use Orchestra\Database\ConsoleServiceProvider;

class TestCase extends \Orchestra\Testbench\TestCase
{
    /**
     * @return  void
     */
    public function setUp():void
    {
        parent::setUp();

        $this->artisan('migrate', ['--database' => 'testbench']);

        $this->loadMigrationsFrom(dirname(__DIR__) . '/migrations');

        $this->setUpRoutes();
    }

    /**
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        // Setup default database to use sqlite :memory:
        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);

        // Setup the right User class (using stub)
        $app['config']->set('auth.providers.users.model', User::class);
        $app['config']->set('auth.guards.api.driver', 'jwt');
        $app['config']->set('auth.guards.web.driver', 'jwt');
    }

    /**
     * @param \Illuminate\Foundation\Application $app
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            ConsoleServiceProvider::class,
            ImpersonateServiceProvider::class,
        ];
    }

    /**
     * @return void
     */
    protected function setUpRoutes()
    {
        // Add routes by calling macro
        $this->app['router']->impersonate();

        // Refresh named routes
        $this->app['router']->getRoutes()->refreshNameLookups();
    }
}
