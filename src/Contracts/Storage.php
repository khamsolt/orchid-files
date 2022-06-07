<?php

namespace Khamsolt\Orchid\Files\Contracts;

use Khamsolt\Orchid\Files\Contracts\Data\TransferObject;
use Khamsolt\Orchid\Files\Data\Transfer\AssignmentObject;

interface Storage
{
    public function put(string $key, array $data): void;

    public function forget(string $key): void;

    public function pull(string $key): ?array;

    public function has(string $key): bool;
}
