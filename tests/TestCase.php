<?php

declare(strict_types=1);

namespace Korridor\LaravelHasManySync\Tests;

use Korridor\LaravelHasManySync\ServiceProvider;
use Illuminate\Foundation\Application;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    /**
     * Setup the test environment.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->loadMigrationsFrom(__DIR__ . '/TestEnvironment/Migrations');
    }

    /**
     * Define environment setup.
     *
     * @param Application $app
     * @return void
     */
    protected function getEnvironmentSetUp($app): void
    {
        $app->setBasePath(__DIR__ . '/TestEnvironment');
    }

    /**
     * Get package providers.
     *
     * @param Application $app
     * @return array<int, class-string>
     */
    protected function getPackageProviders($app): array
    {
        return [
            ServiceProvider::class,
        ];
    }
}
