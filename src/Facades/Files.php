<?php

namespace Khamsolt\Orchid\Files\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Khamsolt\OrchidFiles\OrchidFiles
 */
class Files extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'orchid-files';
    }
}
