<?php

namespace Khamsolt\Orchid\Files;

use Orchid\Attachment\Engines\Generator;

class FileGenerator extends Generator
{
    public function path(): string
    {
        /** @var array<string, string|int> $settings */
        $settings = config('orchid-files.storage', []);

        /** @var string $folder */
        $folder = $settings['folder'] ?? 'files';

        $hash = $this->hash();

        /** @var int $step */
        $step = $settings['steps'] ?? 3;

        /** @var int $chars */
        $chars = $settings['chars'] ?? 3;

        $result = substr($hash, 0, $chars);

        for ($i = $chars; $i < $step * $chars; $i += $chars) {
            $result .= '/'.substr($hash, $i, $chars);
        }

        return $this->path = "$folder/$result";
    }
}
