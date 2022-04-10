<?php

namespace Khamsolt\Orchid\Files\Data\Transfer;

use Khamsolt\Orchid\Files\Contracts\Data\TransferObject;
use Spatie\DataTransferObject\DataTransferObject;

class AssignmentObject extends DataTransferObject implements TransferObject
{
    public string $type;

    public string $id;

    public string $group;

    public string $redirect;
}
