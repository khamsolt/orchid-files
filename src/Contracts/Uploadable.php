<?php

namespace Khamsolt\Orchid\Files\Contracts;

use Illuminate\Http\UploadedFile;
use Khamsolt\Orchid\Files\Models\Attachment;

interface Uploadable
{
    public function upload(UploadedFile $uploadedFile, array $data): Attachment;
}
