<?php

namespace Khamsolt\Orchid\Files\Contracts\Entities;

interface Attachmentable
{
    public function getAssigmentType(): string;

    public function getAssigmentId(): int|string;

    public function getAttachmentId(): int|string;

    public function getGroup(): string|null;
}
