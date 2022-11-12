<?php

namespace Khamsolt\Orchid\Files;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Khamsolt\Orchid\Files\Commands\FilesInstallCommand;
use Khamsolt\Orchid\Files\Contracts\Attachable;
use Khamsolt\Orchid\Files\Contracts\Attachmentable;
use Khamsolt\Orchid\Files\Contracts\Permissions;
use Khamsolt\Orchid\Files\Contracts\Repository;
use Khamsolt\Orchid\Files\Contracts\Updatable;
use Khamsolt\Orchid\Files\Contracts\Uploadable;
use Khamsolt\Orchid\Files\Providers\AuthServiceProvider;
use Khamsolt\Orchid\Files\View\Components\Preview;
use Khamsolt\Orchid\Files\View\Components\Thumbnail;
use Orchid\Attachment\Models\Attachment;
use Orchid\Platform\Dashboard;

class FileServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(dirname(__DIR__) . '/config/orchid-files.php', 'orchid-files');

        $this->bindDependencies()->registerProviders();
    }

    public function boot(): void
    {
        Dashboard::useModel(Attachment::class, Models\Attachment::class);

        $this->registerViews();

        if ($this->app->runningInConsole()) {
            $this->commands([
                FilesInstallCommand::class,
            ]);

            $this->publishes([
                dirname(__DIR__) . '/database/migrations' => database_path('migrations'),
            ], 'migrations');

            $this->publishes([
                dirname(__DIR__) . '/config/orchid-files.php' => config_path('orchid-files.php'),
            ], 'config');

            $this->publishes([
                dirname(__DIR__) . '/resources/views' => resource_path('views/vendor/orchid_files'),
            ], 'views');
        }
    }

    public function provides(): array
    {
        return [
            AuthServiceProvider::class,
        ];
    }

    protected function registerProviders(): self
    {
        foreach ($this->provides() as $provide) {
            $this->app->register($provide);
        }

        return $this;
    }

    protected function bindDependencies(): self
    {
        $config = $this->app->make('config');

        assert($config instanceof \Illuminate\Contracts\Config\Repository);

        /** @var array<string, mixed> $settings */
        $settings = $config->get('orchid-files.bind');

        $this->app->bind(Permissions::class, FilePermission::class);

        /** @var class-string $searchClass */
        $searchClass = $settings['search'] ?? FileRepository::class;

        $this->app->bind(Repository::class, $searchClass);

        /** @var class-string $updateClass */
        $updateClass = $settings['update'] ?? FileService::class;

        $this->app->bind(Updatable::class, $updateClass);

        /** @var class-string $uploadClass */
        $uploadClass = $settings['upload'] ?? FileService::class;

        $this->app->bind(Uploadable::class, $uploadClass);

        /** @var class-string $attachmentClass */
        $attachmentClass = $settings['attach'] ?? FileService::class;

        $this->app->bind(Attachable::class, $attachmentClass);

        /** @var class-string $attachmentableClass */
        $attachmentableClass = $settings['attachmentable'] ?? FileAttachment::class;

        $this->app->bind(Attachmentable::class, $attachmentableClass);

        $this->app->bind('orchid-files', FileSettings::class);

        return $this;
    }

    protected function registerViews(): self
    {
        Blade::componentNamespace('Khamsolt\\Orchid\\Files\\View\\Components', 'orchid-files');

        $this->loadViewsFrom(dirname(__DIR__) . '/resources/views', 'orchid-files');

        $this->loadViewComponentsAs('orchid-files', [
            Preview::class,
            Thumbnail::class,
        ]);

        return $this;
    }
}
