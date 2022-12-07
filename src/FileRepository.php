<?php

namespace Khamsolt\Orchid\Files;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Khamsolt\Orchid\Files\Contracts\Repository;
use Khamsolt\Orchid\Files\Enums\Type;
use Khamsolt\Orchid\Files\Models\Attachment;

class FileRepository implements Repository
{
    /**
     * @param Type|null $type
     * @return LengthAwarePaginator<Attachment>
     */
    public function paginate(Type $type = null): LengthAwarePaginator
    {
        $builder = Attachment::with(['user'])
            ->filters()
            ->defaultSort('created_at', 'desc');

        if ($type !== null) {
            $builder->when($type === Type::IMAGES, fn ($query) => $query->whereIn('extension', Attachment::IMAGE_EXTENSIONS));
        }

        return $builder->paginate();
    }

    public function find(int $id): Attachment
    {
        $attachment = Attachment::with(['user'])->where('attachments.id', '=', $id)->firstOrFail();

        assert($attachment instanceof Attachment);

        return $attachment;
    }
}
