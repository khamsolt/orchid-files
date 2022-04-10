<?php

namespace Khamsolt\Orchid\Files\Contracts\Entities;

use Orchid\Platform\ItemPermission;

interface Permissions
{
    public static function accessViewFile(): string;

    public static function accessFileList(): string;

    public static function accessFileAttachments(): string;

    public static function accessFileAssignment(): string;

    public static function accessFileUpdates(): string;

    public static function accessFileUploads(): string;

    public function getItemPermission(): ItemPermission;
}
