<?php

namespace Khamsolt\Orchid\Files\Tests;

use Artisan;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Schema\Blueprint;
use Khamsolt\Orchid\Files\FileGenerator;
use Khamsolt\Orchid\Files\FileServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;
use Orchid\Platform\Providers\FoundationServiceProvider;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'Khamsolt\\Orchid\\Files\\Database\\Factories\\'.class_basename($modelName).'Factory'
        );

        $this->setUpDatabase($this->app);
        $this->startSession();
    }

    /**
     * Set up the database.
     *
     * @param \Illuminate\Foundation\Application $app
     */
    protected function setUpDatabase($app)
    {
        $app['db']->connection()->getSchemaBuilder()->create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });

        $this->loadMigrationsFrom(dirname(__DIR__) . '/vendor/orchid/platform/database/migrations');

        Artisan::call('vendor:publish', [
            '--provider' => FoundationServiceProvider::class,
            '--tag' => 'config'
        ]);

        Artisan::call('migrate', ['--force' => true]);
    }

    protected function getPackageProviders($app)
    {
        return [
            FoundationServiceProvider::class,
            FileServiceProvider::class,
        ];
    }

    /**
     * Set up the environment.
     *
     * @param \Illuminate\Foundation\Application $app
     */
    protected function getEnvironmentSetUp($app)
    {
        //$app['path.lang'] = __DIR__ . '/lang';

        $app['config']->set('database.default', 'pgsql');
        $app['config']->set('database.connections.pgsql', [
            'driver' => 'pgsql',
            'host' => 'postgres-postgis',
            'database' => 'opencode_test_db',
            'username' => 'default',
            'password' => 'secret',
        ]);

        $app['config']->set('platform.attachment.generator', FileGenerator::class);
    }

    /**
     * @inheritdoc
     */
    protected function resolveApplicationConfiguration($app)
    {
        parent::resolveApplicationConfiguration($app);

        $app['config']->set('session.drive', 'array');
    }
}
