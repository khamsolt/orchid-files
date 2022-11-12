<?php

namespace Khamsolt\Orchid\Files\Contracts;

interface Updatable
{
    /**
     * @param int $id
     * @param array<string, mixed> $data
     * @return bool
     */
    public function update(int $id, array $data): bool;
}
