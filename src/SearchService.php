<?php

namespace Khamsolt\Orchid\Files;

use Khamsolt\Orchid\Files\Contracts\Searchable;
use Khamsolt\Orchid\Files\Models\Attachment;
use Illuminate\Pagination\LengthAwarePaginator;

class SearchService implements Searchable
{
    public function paginate(): LengthAwarePaginator
    {
        $builder = Attachment::with(['user'])->filters()->defaultSort('created_at', 'desc');

        $result = $builder->paginate();

        return $result;
    }
}
