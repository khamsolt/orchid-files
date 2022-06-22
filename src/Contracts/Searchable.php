<?php

namespace Khamsolt\Orchid\Files\Contracts;

use Illuminate\Pagination\LengthAwarePaginator;
use Khamsolt\Orchid\Files\Models\Attachment;

interface Searchable
{
    public function paginate(): LengthAwarePaginator;

    public function find(int $id): Attachment;
}
