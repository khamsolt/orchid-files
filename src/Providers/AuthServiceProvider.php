<?php

namespace Khamsolt\Orchid\Files\Providers;

use Illuminate\Support\ServiceProvider;
use Khamsolt\Orchid\Files\Contracts\Entities\Permissions;
use Orchid\Platform\Dashboard;

class AuthServiceProvider extends ServiceProvider
{
    protected Dashboard $dashboard;

    public function boot(Dashboard $dashboard, Permissions $permissions): void
    {
        $this->dashboard = $dashboard;

        $this->app->booted(function () use ($permissions) {
            $this->dashboard->registerPermissions($permissions->getItemPermission());
        });
    }
}
