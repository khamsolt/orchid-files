<?php

namespace Khamsolt\Orchid\Files\Contracts\Data;

interface TransferObject
{
    public function toArray(): array;

    public function except(string ...$keys): static;
}
