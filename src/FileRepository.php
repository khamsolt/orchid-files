<?php

namespace Khamsolt\Orchid\Files;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Khamsolt\Orchid\Files\Contracts\Repository;
use Khamsolt\Orchid\Files\Enums\Type;
use Khamsolt\Orchid\Files\Models\Attachment;

class FileRepository implements Repository
{
    /**
     * @return LengthAwarePaginator<Attachment>
     */
    public function paginate(Type $type = null): LengthAwarePaginator
    {
        $builder = Attachment::with(['user'])
            ->filters()
            ->defaultSort('created_at', 'desc');

        if ($type !== null) {
            $builder->when(
                $type === Type::IMAGES,
                fn ($query) => $query->whereIn('extension', Attachment::IMAGE_EXTENSIONS)
            );
        }

        return $builder->paginate();
    }

    public function find(int $id): Attachment
    {
        $attachment = Attachment::with(['user'])->where('attachments.id', '=', $id)->firstOrFail();

        assert($attachment instanceof Attachment);

        return $attachment;
    }

    public function findMore(array $ids): Collection
    {
        $attachments = Attachment::with(['user'])->whereIn('attachments.id', $ids)->get();

        assert($attachments instanceof Collection);

        return $attachments;
    }
}
