<?php

namespace Khamsolt\Orchid\Files\Contracts;

interface Updatable
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function update(int $id, array $data): bool;
}
