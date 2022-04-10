<?php

namespace Khamsolt\Orchid\Files;

use Illuminate\Contracts\Session\Session;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Khamsolt\Orchid\Files\Contracts\Attachable;
use Khamsolt\Orchid\Files\Contracts\Data\TransferObject;
use Khamsolt\Orchid\Files\Contracts\Updatable;
use Khamsolt\Orchid\Files\Models\Attachment;
use Khamsolt\Orchid\Files\Models\Attachmentable;

class FileService implements Updatable, Attachable
{
    private Session $session;

    public function __construct(Session $session)
    {
        $this->session = $session;
    }

    public function update(int $id, TransferObject $transferObject): bool
    {
        $result = Attachment::query()
            ->where('id', '=', $id)
            ->update($transferObject->toArray());

        return $result;
    }

    public function attach(int $attachmentId, string $type, int $id, ?string $group = null): Model
    {
        $model = new Attachmentable([
            'attachmentable_type' => $type,
            'attachmentable_id' => $id,
            'attachment_id' => $attachmentId,
            'group' => $group,
        ]);

        $model->saveOrFail();

        return $model;
    }

    public function attachMany(array $attachments, string $type, int $id, ?string $group = null): bool
    {
        $data = (new Collection($attachments))->map(fn(int|string $attachmentId) => is_int($attachmentId) ? [
            'attachmentable_type' => $type,
            'attachmentable_id' => $id,
            'attachment_id' => $attachmentId,
            'group' => $group,
        ] : null)->filter();

        $result = Attachmentable::query()->insert($data);

        return $result;
    }

    public function detachAll(string $type, int $id, ?string $group = null): int
    {
        return $this->detach(null, $type, $id, $group);
    }

    public function detach(array|int|null $attachment, string $type, int $id, ?string $group = null): int
    {
        $builder = Attachmentable::where('attachmentable_type', '=', $type)
            ->where('attachmentable_id', '=', $id)
            ->where('group', '=', $group);

        if (is_array($attachment)) {
            return $builder->whereIn('attachment_id', $attachment)->delete();
        }

        if (is_int($attachment)) {
            return $builder->where('attachment_id', '=', $attachment)->delete();
        }

        return $builder->delete();
    }
}
