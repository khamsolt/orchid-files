<?php

namespace Khamsolt\Orchid\Files\Facades;

use Illuminate\Support\Facades\Facade;
use Khamsolt\Orchid\Files\FileService;

/**
 * @see \Khamsolt\Orchid\Files\FileManager
 */
class Files extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return FileService::class;
    }
}
