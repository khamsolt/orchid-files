<?php

namespace Khamsolt\Orchid\Files;

use Khamsolt\Orchid\Files\Contracts\Attachmentable;

class FileAttachment implements Attachmentable
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

    public function getGroup(): ?string
    {
        return 'group';
    }
}
