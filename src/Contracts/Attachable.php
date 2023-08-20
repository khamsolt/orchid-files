<?php

namespace Khamsolt\Orchid\Files\Contracts;

interface Attachable
{
    public function sync(string $modelType, int $modelId, array $ids, string $group = null): void;

    public function attach(int $attachmentId, string $type, int $id, string $group = null): int;

    public function attachMany(array $attachments, string $type, int $id, string $group = null): bool;

    public function detachAll(string $type, int $id, string $group = null): int;

    public function detach(int|array|null $attachment, string $type, int $id, string $group = null): int;
}
