<?php

namespace Khamsolt\Orchid\Files;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Khamsolt\Orchid\Files\Commands\FilesCommand;

class FileServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('orchid-files')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_orchid-files_table')
            ->hasCommand(FilesCommand::class);
    }
}
