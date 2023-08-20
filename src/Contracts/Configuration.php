<?php

namespace Khamsolt\Orchid\Files\Contracts;

use Illuminate\Support\Carbon;
use Khamsolt\Orchid\Files\Enums\Action;

interface Configuration
{
    public function toDatetimeFormat(?Carbon $carbon, string $timezone = null, string $format = null): ?string;

    public function name(): string;

    public function table(): string;

    public function relationTable(): string;

    public function size(): int;

    public function user(string $key): string;

    public function userColumns(): array;

    public function storage(string $key = null): string|array;

    public function routes(): array;

    public function route(Action $action): string;

    public function permissionTitles(): array;

    public function permissionTitle(Action $action): string;

    public function permissionKeys(): array;

    public function permissionAccesses(Action $action = null): array;

    public function permissionKey(Action $action): string;
}
