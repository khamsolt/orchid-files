<?php

namespace Khamsolt\Orchid\Files\Entities;

class Attachmentable implements \Khamsolt\Orchid\Files\Contracts\Entities\Attachmentable
{
    public function getAssigmentType(): string
    {
        return 'attachmentable_type';
    }

    public function getAssigmentId(): int|string
    {
        return 'attachmentable_id';
    }

    public function getAttachmentId(): int|string
    {
        return 'attachment_id';
    }

    public function getGroup(): string|null
    {
        return 'group';
    }
}
