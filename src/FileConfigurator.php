<?php

namespace Khamsolt\Orchid\Files;

use Illuminate\Contracts\Config\Repository;
use Illuminate\Support\Arr;
use Khamsolt\Orchid\Files\Contracts\Configuration;
use Khamsolt\Orchid\Files\Enums\Action;
use Khamsolt\Orchid\Files\Exceptions\FileException;
use Khamsolt\Orchid\Files\Exceptions\IncorrectConfigException;

final class FileConfigurator implements Configuration
{
    private readonly array $configs;

    /**
     * @throws FileException
     */
    public function __construct(
        private readonly Repository $repository
    ) {
        $configs = $this->repository->get('orchid-files');

        if (!is_array($configs)) {
            throw new FileException('Incorrect configurations from the Orchid Files package');
        }

        $this->configs = $configs;
    }

    public function table(): string
    {
        return $this->findAsString('table');
    }

    /**
     * @param  string  $key
     * @return string
     * @throws IncorrectConfigException
     */
    private function findAsString(string $key): string
    {
        /** @var string $result */
        $result = Arr::get($this->configs, $key);

        if (!is_string($result)) {
            $this->error($key);
        }

        return $result;
    }

    /**
     * @throws IncorrectConfigException
     */
    private function error(string $key): void
    {
        throw new IncorrectConfigException($key);
    }

    public function relationTable(): string
    {
        return $this->findAsString('relation_table');
    }

    public function size(): int
    {
        return $this->findAsNumeric('size');
    }

    /**
     * @param  string  $key
     * @return int|float
     * @throws IncorrectConfigException
     */
    private function findAsNumeric(string $key): int|float
    {
        $result = Arr::get($this->configs, $key);

        if (!is_numeric($result)) {
            $this->error($key);
        }

        return $result;
    }

    public function user(string $key): string
    {
        return $this->findAsString("user.$key");
    }

    public function userColumns(): array
    {
        return $this->findAsArray('user.columns');
    }

    /**
     * @param  string  $key
     * @return array
     * @throws IncorrectConfigException
     */
    private function findAsArray(string $key): array
    {
        $result = Arr::get($this->configs, $key);

        if (!is_array($result)) {
            $this->error($key);
        }

        return $result;
    }

    /**
     * @throws IncorrectConfigException
     */
    public function storage(string $key = null): string|array
    {
        if (is_null($key)) {
            return $this->findAsArray('storage');
        }

        return $this->findAsString("storage.$key");
    }

    /**
     * @throws IncorrectConfigException
     */
    public function routes(): array
    {
        return $this->findAsArray('routes');
    }

    /**
     * @throws IncorrectConfigException
     */
    public function route(Action $action): string
    {
        return $this->findAsString("routes.{$action->value}");
    }

    /**
     * @param  Action|null  $action
     * @return array<string, string>
     * @throws IncorrectConfigException
     */
    public function permissionTitles(Action $action = null): array
    {
        return $this->findAsArray('permissions.titles');
    }

    /**
     * @return array<string, string>
     * @throws IncorrectConfigException
     */
    public function permissionKeys(): array
    {
        return $this->findAsArray('permissions.keys');
    }

    /**
     * @return array<string, string[]>|string[]
     */
    public function permissionAccesses(Action $action = null): array
    {
        if (is_null($action)) {
            return $this->findAsArray('permissions.accesses');
        }

        return $this->findAsArray("permissions.accesses.{$action->value}");
    }

    /**
     * @param  Action  $action
     * @return string
     * @throws IncorrectConfigException
     */
    public function permissionKey(Action $action): string
    {
        return $this->findAsString("permissions.keys.{$action->value}");
    }

    /**
     * @throws IncorrectConfigException
     */
    public function permissionTitle(Action $action): string
    {
        return $this->findAsString("permissions.titles.{$action->value}");
    }

    /**
     * @throws IncorrectConfigException
     */
    public function name(): string
    {
        return $this->findAsString('name');
    }
}
