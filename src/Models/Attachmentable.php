<?php

namespace Khamsolt\Orchid\Files\Models;

class Attachmentable extends \Orchid\Attachment\Models\Attachmentable
{
    protected $fillable = [
        'attachmentable_type',
        'attachmentable_id',
        'attachment_id',
        'group',
    ];
}
