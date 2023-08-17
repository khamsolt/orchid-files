<?php

namespace Khamsolt\Orchid\Files\Providers;

use Illuminate\Support\ServiceProvider;
use Khamsolt\Orchid\Files\Contracts\Authorization;
use Orchid\Platform\Dashboard;

class AuthServiceProvider extends ServiceProvider
{
    public function boot(Dashboard $dashboard, Authorization $authorization): void
    {
        $this->app->booted(function () use ($dashboard, $authorization) {
            $dashboard->registerPermissions($authorization->getItemPermission());
        });
    }
}
