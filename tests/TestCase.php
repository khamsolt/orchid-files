<?php

namespace Khamsolt\Orchid\Files\Tests;

use Artisan;
use Khamsolt\Orchid\Files\FileGenerator;
use Khamsolt\Orchid\Files\FileServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;
use Orchid\Platform\Providers\FoundationServiceProvider;

class TestCase extends Orchestra
{
    protected $loadEnvironmentVariables = false;

    protected function setUp(): void
    {
        parent::setUp();

        Artisan::call('vendor:publish', [
            '--provider' => FoundationServiceProvider::class,
            '--tag' => 'config',
        ]);

        Artisan::call('orchid-files:install');
    }

    protected function getPackageProviders($app): array
    {
        return [
            FoundationServiceProvider::class,
            FileServiceProvider::class,
        ];
    }

    protected function defineDatabaseMigrations(): void
    {
        $this->loadLaravelMigrations();

        $this->beforeApplicationDestroyed(fn() => $this->artisan('db:wipe'));
    }

    protected function getEnvironmentSetUp($app): void
    {
        $app['config']->set('database.connections.pgsql', [
            'driver' => 'pgsql',
            'host' => 'postgres-postgis',
            'database' => 'opencode_test_db',
            'username' => 'default',
            'password' => 'secret',
        ]);

        $app['config']->set('platform.attachment.generator', FileGenerator::class);
    }
}
