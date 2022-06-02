<?php

namespace Khamsolt\Orchid\Files\Commands;

use Illuminate\Console\Command;
use Khamsolt\Orchid\Files\FileServiceProvider;

class FilesInstallCommand extends Command
{
    public $signature = 'orchid-files:install';

    public $description = 'User package install';

    public function handle(): int
    {
        $this->comment('Installation started. Please wait...');

        $this->callSilent('vendor:publish', [
            '--provider' => FileServiceProvider::class,
            '--tag' => [
                'config',
                'migrations',
            ],
        ]);

        $this->info('Completed!');

        return self::SUCCESS;
    }
}
