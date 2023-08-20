<?php

namespace Khamsolt\Orchid\Files\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Khamsolt\Orchid\Files\Enums\Type;
use Khamsolt\Orchid\Files\Models\Attachment;

interface Repository
{
    /**
     * @return LengthAwarePaginator<Attachment>
     */
    public function paginate(Type $type = null): LengthAwarePaginator;

    public function find(int $id): Attachment;

    /**
     * @return Collection<int, Attachment>
     */
    public function findMore(array $ids): Collection;
}
