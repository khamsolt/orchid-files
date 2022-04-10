<?php

namespace Khamsolt\Orchid\Files\Contracts;

use Illuminate\Pagination\LengthAwarePaginator;

interface Searchable
{
    public function paginate(): LengthAwarePaginator;
}
