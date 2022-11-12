<?php

namespace Khamsolt\Orchid\Files\Models;

use Illuminate\Support\Carbon;
use Orchid\Metrics\Chartable;
use Orchid\Screen\AsSource;


/**
 * @property int $id
 * @property string $name
 * @property string $original_name
 * @property string $mime
 * @property string $extension
 * @property string $disk
 * @property string $hash
 * @property string $size
 * @property int|null $sort
 * @property string|null $group
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
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
        'hash',
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
        'hash',
        'created_at',
        'updated_at',
    ];

    public function sizeToKb(): float
    {
        return ($size = (int)$this->size) > 0 ? round($size / 1024, 2) : 0;
    }

    public function thumbnail(): string
    {
        $default = 'https://via.placeholder.com/150?text=.' . strtoupper($this->extension);

        return $this->isImage() ? $this->url() ?? $default : $default;
    }

    public function isImage(): bool
    {
        $exts = ['gif', 'jpe', 'jpeg', 'jpg', 'svg', 'ico', 'png'];

        return in_array(strtolower($this->extension), $exts);
    }
}
