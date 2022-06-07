<?php

namespace Khamsolt\Orchid\Files;

use Khamsolt\Orchid\Files\Contracts\Attachable;
use Khamsolt\Orchid\Files\Contracts\Data\TransferObject;
use Khamsolt\Orchid\Files\Contracts\Updatable;
use Khamsolt\Orchid\Files\Models\Attachment;
use Khamsolt\Orchid\Files\Models\Attachmentable;

class FileService implements Updatable, Attachable
{
    public function update(int $id, TransferObject $transferObject): bool
    {
        $result = Attachment::query()
            ->where('id', '=', $id)
            ->update($transferObject->toArray());

        return (bool)$result;
    }

    public function attach(int $attachmentId, string $type, int $id, ?string $group = null): int
    {
        $data = [
            'attachmentable_type' => $type,
            'attachmentable_id' => $id,
            'attachment_id' => $attachmentId,
            'group' => $group
        ];

        $model = new Attachmentable();

        $model->newQuery()->updateOrInsert($data);

        return 0;
    }

    public function attachMany(array $attachments, string $type, int $id, ?string $group = null): bool
    {
        $data = array_map(fn(int $attachmentId) => [
            'attachmentable_type' => $type,
            'attachmentable_id' => $id,
            'attachment_id' => $attachmentId,
            'group' => $group,
        ], $attachments);

        $data = array_filter($data);

        $model = new Attachmentable();

        return $model->newQuery()->insert($data);
    }

    public function detachAll(string $type, int $id, ?string $group = null): int
    {
        return $this->detach(null, $type, $id, $group);
    }

    public function detach(array|int|null $attachment, string $type, int $id, ?string $group = null): int
    {
        $model = new Attachmentable();

        $builder = $model->newQuery()
            ->where('attachmentable_type', '=', $type)
            ->where('attachmentable_id', '=', $id)
            ->where('group', '=', $group);

        if (is_array($attachment)) {
            $builder->whereIn('attachment_id', $attachment);
        } elseif (is_int($attachment)) {
            $builder->where('attachment_id', '=', $attachment);
        }

        return $builder->delete();
    }
}
