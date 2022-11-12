<?php

namespace Khamsolt\Orchid\Files\Contracts;

use Orchid\Platform\ItemPermission;

interface Permissions
{
    public function accessViewFile(): iterable;

    public function accessFileList(): iterable;

    public function accessFileAttachments(): iterable;

    public function accessFileAssignment(): iterable;

    public function accessFileUpdates(): iterable;

    public function accessFileUploads(): iterable;

    public function getItemPermission(): ItemPermission;
}
