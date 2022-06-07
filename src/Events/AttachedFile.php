<?php

namespace Khamsolt\Orchid\Files\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AttachedFile
{
    use Dispatchable, SerializesModels;

    public function __construct(public string $type,
                                public int    $id,
                                public string $redirect,
                                public string $group)
    {
    }

    /**
     * @return array<string, string|int>
     */
    public function toArray(): array
    {
        return [
            'type'     => $this->type,
            'id'       => $this->id,
            'redirect' => $this->redirect,
            'group'    => $this->group
        ];
    }
}
