<?php

namespace Khamsolt\Orchid\Files\Contracts;

use Illuminate\Http\RedirectResponse;
use Khamsolt\Orchid\Files\Contracts\Data\TransferObject;
use Khamsolt\Orchid\Files\Data\Transfer\AssignmentObject;

interface Assignable
{
    public const KEY_FILE_ASSIGNMENT = 'file_assignment';

    public function putWithRedirect(AssignmentObject $attachmentableObject): RedirectResponse;

    public function retrieve(string $key): TransferObject | AssignmentObject;

    public function has(): bool;
}
