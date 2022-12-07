<?php

namespace Khamsolt\Orchid\Files;

use Illuminate\Http\Request;
use Orchid\Screen\Action;

final class FileFeatures
{
    /**
     * @var array<int, callable(Request):Action>
     */
    private static array $fileListScreenCommandsIntegrations = [];

    /**
     * @param callable(Request):Action $closure
     * @return void
     */
    public static function setFileListScreenCommandsIntegration(callable $closure): void
    {
        self::$fileListScreenCommandsIntegrations[] = $closure;
    }

    /**
     * @return array<int, Action>
     */
    public static function resolveFileListScreenCommandsIntegration(Request $request): array
    {
        $integrations = self::$fileListScreenCommandsIntegrations;

        $result = [];

        if (count($integrations) === 0) {
            return $result;
        }

        foreach ($integrations as $integration) {
            if (!is_callable($integration)) {
                continue;
            }

            $result[] = ($integration)($request);
        }

        return $result;
    }
}
