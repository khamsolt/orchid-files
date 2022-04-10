<?php

namespace Khamsolt\Orchid\Files;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Khamsolt\Orchid\Files\Contracts\Assignable;
use Khamsolt\Orchid\Files\Contracts\Attachable;
use Khamsolt\Orchid\Files\Contracts\Entities\Attachmentable;
use Khamsolt\Orchid\Files\Contracts\Entities\Permissions;
use Khamsolt\Orchid\Files\Contracts\Searchable;
use Khamsolt\Orchid\Files\Contracts\Storage;
use Khamsolt\Orchid\Files\Contracts\Updatable;
use Khamsolt\Orchid\Files\Data\Storage\SessionStorage;
use Khamsolt\Orchid\Files\Providers\AuthServiceProvider;
use Khamsolt\Orchid\Files\View\Components\Preview;
use Khamsolt\Orchid\Files\View\Components\Thumbnail;

class FileServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->bindDependencies()
            ->registerProviders();

        $this->mergeConfigFrom(
            __DIR__ . '/../config/orchid-files.php', 'orchid-files'
        );
    }

    public function boot()
    {
        $this->registerConfig()
            ->registerDatabase()
            ->registerViews();
    }

    protected function registerConfig(): self
    {
        $this->publishes([
            __DIR__ . '/../orchid-files.php' => config_path('orchid-files.php'),
        ], 'config');

        return $this;
    }

    protected function registerDatabase(): self
    {
        $this->publishes([
            __DIR__ . '/../database/migrations' => database_path('migrations')
        ], 'orchid-files');

        return $this;
    }

    protected function registerViews(): self
    {
        Blade::componentNamespace('Khamsolt\\Orchid\\Files\\View\\Components', 'orchid-files');

        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'orchid-files');

        $this->loadViewComponentsAs('orchid-files', [
            Preview::class,
            Thumbnail::class
        ]);

        $this->publishes([
            __DIR__ . '/../resources/views' => resource_path('views/vendor/orchid_files'),
        ], 'views');

        return $this;
    }

    protected function bindDependencies(): self
    {
        $config = $this->app->make('config');

        $this->app->bind(Storage::class,        $config->get('orchid-files.storage', SessionStorage::class));
        $this->app->bind(Searchable::class,     $config->get('orchid-files.search', SearchService::class));
        $this->app->bind(Updatable::class,      $config->get('orchid-files.update', FileService::class));
        $this->app->bind(Attachable::class,     $config->get('orchid-files.attach', FileService::class));
        $this->app->bind(Assignable::class,     $config->get('orchid-files.assign', FileAssigment::class));
        $this->app->bind(Permissions::class,    $config->get('orchid-files.entities.permissions', Authorization\Permissions::class));
        $this->app->bind(Attachmentable::class, $config->get('orchid-files.entities.attachmentable', Entities\Attachmentable::class));

        return $this;
    }

    protected function registerProviders(): self
    {
        foreach ($this->provides() as $provide) {
            $this->app->register($provide);
        }

        return $this;
    }

    public function provides(): array
    {
        return [
            AuthServiceProvider::class
        ];
    }
}
