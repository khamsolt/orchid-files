<?php

namespace Khamsolt\Orchid\Files\Models;

use Illuminate\Support\Carbon;
use Orchid\Filters\Types\Like;
use Orchid\Filters\Types\Where;
use Orchid\Filters\Types\WhereDateStartEnd;
use Orchid\Metrics\Chartable;
use Orchid\Screen\AsSource;

/**
 * @property-read int $id
 * @property-read string $name
 * @property-read string $mime
 * @property-read string $extension
 * @property-read string $path
 * @property string $original_name
 * @property string|null $description
 * @property string|null $alt
 * @property string|null $disk
 * @property string|null $hash
 * @property int $size
 * @property int|null $sort
 * @property string|null $group
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class Attachment extends \Orchid\Attachment\Models\Attachment
{
    use AsSource;
    use Chartable;

    public const IMAGE_EXTENSIONS = ['gif', 'jpe', 'jpeg', 'jpg', 'svg', 'ico', 'png'];

    public const GROUP_THUMBNAIL = 'thumbnail';

    protected $allowedFilters = [
        'id' => Where::class,
        'name' => Like::class,
        'original_name' => Like::class,
        'mime' => Where::class,
        'extension' => Where::class,
        'disk' => Where::class,
        'size' => Where::class,
        'sort' => Where::class,
        'group' => Like::class,
        'hash' => Like::class,
        'created_at' => WhereDateStartEnd::class,
        'updated_at' => WhereDateStartEnd::class,
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
        return ($size = (int) $this->size) > 0 ? round($size / 1024, 2) : 0;
    }

    public function thumbnail(): string
    {
        $default = 'https://via.placeholder.com/150?text=.'.strtoupper($this->extension);

        return $this->isImage() ? $this->url() ?? $default : $default;
    }

    public function isImage(): bool
    {
        return in_array(strtolower($this->extension), self::IMAGE_EXTENSIONS);
    }
}
