<?php

namespace Khamsolt\Orchid\Files\Contracts;

interface Translation
{
    public function get(string $text, array $replace = [], string $locale = null): string;
}
