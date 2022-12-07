<?php

namespace Khamsolt\Orchid\Files\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Khamsolt\Orchid\Files\Enums\Type;
use Khamsolt\Orchid\Files\Models\Attachment;

interface Repository
{
    /**
     * @param Type|null $type
     * @return LengthAwarePaginator<Attachment>
     */
    public function paginate(Type $type = null): LengthAwarePaginator;

    public function find(int $id): Attachment;
}
