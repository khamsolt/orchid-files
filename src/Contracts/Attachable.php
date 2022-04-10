<?php

namespace Khamsolt\Orchid\Files\Contracts;

use Illuminate\Database\Eloquent\Model;

interface Attachable
{
    public function attach(int $attachmentId, string $type, int $id, ?string $group = null): Model;

    public function attachMany(array $attachments, string $type, int $id, ?string $group = null): bool;

    public function detachAll(string $type, int $id, ?string $group = null): int;

    public function detach(int|array|null $attachment, string $type, int $id, ?string $group = null): int;
}
