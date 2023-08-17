<?php

namespace Khamsolt\Orchid\Files\Exceptions;

class IncorrectTranslateKeyException extends FileException
{
    public function __construct(string $key)
    {
        parent::__construct("Incorrect translation key [$key] from Orchid Files package");
    }
}
