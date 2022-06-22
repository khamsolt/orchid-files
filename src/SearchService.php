<?php

namespace Khamsolt\Orchid\Files;

use Illuminate\Pagination\LengthAwarePaginator;
use Khamsolt\Orchid\Files\Contracts\Searchable;
use Khamsolt\Orchid\Files\Models\Attachment;

class SearchService implements Searchable
{
    public function paginate(): LengthAwarePaginator
    {
        $builder = Attachment::with(['user'])->filters()->defaultSort('created_at', 'desc');

        $result = $builder->paginate();

        return $result;
    }

    public function find(int $id): Attachment
    {
        $attachment = Attachment::with(['user'])->findOrFail($id);

        return $attachment;
    }
}
