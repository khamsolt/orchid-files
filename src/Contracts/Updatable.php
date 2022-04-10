<?php

namespace Khamsolt\Orchid\Files\Contracts;

use Khamsolt\Orchid\Files\Contracts\Data\TransferObject;

interface Updatable
{
    public function update(int $id, TransferObject $transferObject): bool;
}
