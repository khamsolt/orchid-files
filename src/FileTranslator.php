<?php

namespace Khamsolt\Orchid\Files;

use Illuminate\Contracts\Translation\Translator as LaravelTranslator;
use Khamsolt\Orchid\Files\Contracts\Translation;
use Khamsolt\Orchid\Files\Exceptions\IncorrectTranslateKeyException;

final class FileTranslator implements Translation
{
    public function __construct(
        private readonly LaravelTranslator $translator
    ) {
    }

    /**
     * @throws IncorrectTranslateKeyException
     */
    public function get(string $text, array $replace = [], string $locale = null): string
    {
        /** @var string $result */
        $result = $this->translator->get($text, $replace, $locale);

        if (! is_string($result)) {
            throw new IncorrectTranslateKeyException($text);
        }

        return $result;
    }
}
