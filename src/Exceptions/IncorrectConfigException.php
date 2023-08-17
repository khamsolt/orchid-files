<?php

namespace Khamsolt\Orchid\Files\Exceptions;

class IncorrectConfigException extends FileException
{
    public function __construct(string $key)
    {
        parent::__construct("Incorrect configuration for key [$key] from the Orchid Files package");
    }
}
