<?php

namespace Khamsolt\Orchid\Files\Contracts;

use Khamsolt\Orchid\Files\Enums\Action;
use Orchid\Platform\ItemPermission;

interface Authorization
{
    public function getItemPermission(): ItemPermission;

    public function authorize(Action $action): array;
}
