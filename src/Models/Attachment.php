<?php

namespace Khamsolt\Orchid\Files\Models;

use Orchid\Metrics\Chartable;
use Orchid\Screen\AsSource;

class Attachment extends \Orchid\Attachment\Models\Attachment
{
    use AsSource;
    use Chartable;

    public const GROUP_THUMBNAIL = 'thumbnail';

    protected $allowedFilters = [
        'id',
        'name',
        'original_name',
        'mime',
        'extension',
        'disk',
        'size',
        'sort',
        'group',
        'created_at',
        'updated_at',
    ];

    protected $allowedSorts = [
        'id',
        'name',
        'original_name',
        'mime',
        'extension',
        'disk',
        'size',
        'sort',
        'group',
        'created_at',
        'updated_at',
    ];

    public function sizeToKb(): float
    {
        return $this->size > 0 ? round($this->size / 1024, 2) : 0;
    }

    public function thumbnail(): string
    {
        return $this->isImage() ? $this->url() : 'https://via.placeholder.com/150?text=.' . strtoupper($this->extension);
    }

    public function isImage(): bool
    {
        $exts = ['gif', 'jpe', 'jpeg', 'jpg', 'svg', 'ico', 'png'];

        return in_array(strtolower($this->extension), $exts);
    }
}
