<?php

namespace Khamsolt\Orchid\Files\Models;

/**
 * @property string $attachmentable_type
 * @property string $attachmentable_id
 * @property string $attachment_id
 * @property string $group
 */
class Attachmentable extends \Orchid\Attachment\Models\Attachmentable
{
    protected $fillable = [
        'attachmentable_type',
        'attachmentable_id',
        'attachment_id',
        'group',
    ];
}
