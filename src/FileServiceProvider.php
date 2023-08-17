<?php

namespace Khamsolt\Orchid\Files;

use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Khamsolt\Orchid\Files\Commands\FilesInstallCommand;
use Khamsolt\Orchid\Files\Contracts\Attachable;
use Khamsolt\Orchid\Files\Contracts\Attachmentable;
use Khamsolt\Orchid\Files\Contracts\Authorization;
use Khamsolt\Orchid\Files\Contracts\Configuration;
use Khamsolt\Orchid\Files\Contracts\Repository;
use Khamsolt\Orchid\Files\Contracts\Translation;
use Khamsolt\Orchid\Files\Contracts\Updatable;
use Khamsolt\Orchid\Files\Contracts\Uploadable;
use Khamsolt\Orchid\Files\Providers\AuthServiceProvider;
use Khamsolt\Orchid\Files\View\Components\Preview;
use Khamsolt\Orchid\Files\View\Components\Thumbnail;
use Orchid\Attachment\Models\Attachment;
use Orchid\Platform\Dashboard;

final class FileServiceProvider extends ServiceProvider
{
    /**
     * @return void
     * @throws BindingResolutionException
     */
    public function register(): void
    {
        $this->mergeConfigFrom(dirname(__DIR__).'/config/orchid-files.php', 'orchid-files');

        $this->bindDependencies();

        $this->registerProviders();
    }

    /**
     * @throws BindingResolutionException
     */
    private function bindDependencies(): void
    {
        /** @var Config $config */
        $config = $this->app->make('config');

        /** @var array<string, class-string> $settings */
        $settings = $config->get('orchid-files.bind');

        $this->app->bind(Configuration::class, $settings['configuration'] ?? FileConfigurator::class);

        $this->app->bind(Translation::class, $settings['translation'] ?? FileTranslator::class);

        $this->app->bind(Authorization::class, $settings['authorization'] ?? FileAuthorize::class);

        $this->app->bind(Repository::class, $settings['search'] ?? FileRepository::class);

        $this->app->bind(Updatable::class, $settings['update'] ?? FileService::class);

        $this->app->bind(Uploadable::class, $settings['upload'] ?? FileService::class);

        $this->app->bind(Attachable::class, $settings['attach'] ?? FileService::class);

        $this->app->bind(Attachmentable::class, $settings['attachmentable'] ?? FileAttachment::class);

        $this->app->bind('orchid-files', FileSettings::class);
    }

    private function registerProviders(): void
    {
        foreach ($this->provides() as $provide) {
            $this->app->register($provide);
        }
    }

    public function provides(): array
    {
        return [
            AuthServiceProvider::class,
        ];
    }

    public function boot(Dashboard $dashboard): void
    {
        $dashboard::useModel(Attachment::class, Models\Attachment::class);

        $this->registerViews();

        if ($this->app->runningInConsole()) {
            $this->commands([
                FilesInstallCommand::class,
            ]);

            $this->publishes([
                dirname(__DIR__).'/database/migrations' => database_path('migrations'),
            ], 'migrations');

            $this->publishes([
                dirname(__DIR__).'/config/orchid-files.php' => config_path('orchid-files.php'),
            ], 'config');

            $this->publishes([
                dirname(__DIR__).'/resources/views' => resource_path('views/vendor/orchid_files'),
            ], 'views');
        }
    }

    private function registerViews(): void
    {
        Blade::componentNamespace('Khamsolt\\Orchid\\Files\\View\\Components', 'orchid-files');

        $this->loadViewsFrom(dirname(__DIR__).'/resources/views', 'orchid-files');

        $this->loadViewComponentsAs('orchid-files', [
            Preview::class,
            Thumbnail::class,
        ]);
    }
}
