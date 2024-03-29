<?php

namespace Khamsolt\Orchid\Files;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\UploadedFile;
use Khamsolt\Orchid\Files\Contracts\Attachable;
use Khamsolt\Orchid\Files\Contracts\Updatable;
use Khamsolt\Orchid\Files\Contracts\Uploadable;
use Khamsolt\Orchid\Files\Models\Attachment;
use Khamsolt\Orchid\Files\Models\Attachmentable;
use League\Flysystem\FilesystemException;
use Orchid\Attachment\File;
use Throwable;

final class FileService implements Updatable, Attachable, Uploadable
{
    public function sync(string $modelType, int $modelId, array $ids, string $group = null): void
    {
        $query = Attachmentable::query()
            ->where('attachmentable_type', '=', $modelType)
            ->where('attachmentable_id', '=', $modelId)
            ->when(! empty($group), fn (Builder $builder) => $builder->where('group', '=', $group));

        $duplicates = $query->clone()
            ->whereIn('attachment_id', $ids)
            ->get();

        $attachedIds = $ids;

        if ($duplicates->count() > 0) {
            $query->whereNotIn('id', $duplicates)->delete();

            $attachedIds = $duplicates->pluck('attachment_id')->diff($ids);
        }

        if (count($attachedIds) === 0) {
            $this->detachAll($modelType, $modelId, $group);

            return;
        }

        $this->attachMany((array) $attachedIds, $modelType, $modelId, $group);
    }

    public function detachAll(string $type, int $id, string $group = null): int
    {
        return $this->detach(null, $type, $id, $group);
    }

    public function detach(array|int|null $attachment, string $type, int $id, string $group = null): int
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

        /** @var int $result */
        $result = $builder->delete();

        return $result;
    }

    public function attachMany(array $attachments, string $type, int $id, string $group = null): bool
    {
        $data = array_map(static fn (int $attachmentId) => [
            'attachmentable_type' => $type,
            'attachmentable_id' => $id,
            'attachment_id' => $attachmentId,
            'group' => $group,
        ], $attachments);

        $data = array_filter($data);

        return Attachmentable::query()->insert($data);
    }

    public function update(int $id, array $data): bool
    {
        $result = Attachment::query()
            ->where('id', '=', $id)
            ->update($data);

        return (bool) $result;
    }

    public function attach(int $attachmentId, string $type, int $id, string $group = null): int
    {
        $data = [
            'attachmentable_type' => $type,
            'attachmentable_id' => $id,
            'attachment_id' => $attachmentId,
            'group' => $group,
        ];

        $model = new Attachmentable();

        $model->newQuery()->updateOrInsert($data);

        return 0;
    }

    /**
     * @throws FilesystemException
     * @throws Throwable
     */
    public function upload(UploadedFile $uploadedFile, array $data): Attachment
    {
        $file = new File($uploadedFile);

        $attachment = $file->load();

        assert($attachment instanceof Attachment);

        if ($userId = $data['user_id'] ?? null) {
            $attachment->setAttribute('user_id', $userId);
        }

        if (! empty($data)) {
            $attachment->fill($data);

            $attachment->saveOrFail();
        }

        return $attachment;
    }
}
