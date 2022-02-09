<?php

namespace Korridor\LaravelHasManySync\Tests;

use Alfa6661\EloquentHasManySync\ServiceProvider;
use Illuminate\Foundation\Application;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    public function setUp(): void
    {
        parent::setUp();

        $this->loadMigrationsFrom(__DIR__.'/TestEnvironment/Migrations');
    }

    protected function getEnvironmentSetUp($app)
    {
        $app->setBasePath(__DIR__.'/TestEnvironment');
    }

    /**
     * @param  Application  $app
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            ServiceProvider::class,
        ];
    }
}
