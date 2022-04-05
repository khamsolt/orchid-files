<?php

namespace Khamsolt\Orchid\Files\Commands;

use Illuminate\Console\Command;

class FilesCommand extends Command
{
    public $signature = 'orchid:files';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
