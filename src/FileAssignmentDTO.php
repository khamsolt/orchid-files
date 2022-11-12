<?php

namespace Khamsolt\Orchid\Files;

use Khamsolt\Orchid\Files\Contracts\Data\TransferObject;
use Spatie\DataTransferObject\DataTransferObject;

class FileAssignmentDTO extends DataTransferObject
{
    public string $type;

    public string $id;

    public string $group;

    public string $redirect;
}