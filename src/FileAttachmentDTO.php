<?php

namespace Khamsolt\Orchid\Files;

use Spatie\DataTransferObject\Attributes\MapFrom;
use Spatie\DataTransferObject\Attributes\MapTo;
use Spatie\DataTransferObject\DataTransferObject;

class FileAttachmentDTO extends DataTransferObject
{
    #[MapFrom('user_id')]
    #[MapTo('user_id')]
    public string|null $userId;

    #[MapFrom('original_name')]
    #[MapTo('original_name')]
    public string|null $originalName;

    public int|null $sort;

    public string|null $description;

    public string|null $alt;

    public string|null $group;
}
