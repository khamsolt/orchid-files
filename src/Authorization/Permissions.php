<?php

namespace Khamsolt\Orchid\Files\Authorization;

use Illuminate\Config\Repository;
use Illuminate\Contracts\Translation\Translator;
use Illuminate\Support\Arr;
use Orchid\Platform\ItemPermission;

class Permissions implements \Khamsolt\Orchid\Files\Contracts\Entities\Permissions
{
    private array $data;

    private Translator $translator;

    public function __construct(Repository $config, Translator $translator)
    {
        $this->translator = $translator;

        $this->data = $config->get('orchid-files.permissions', []);
    }

    public static function accessViewFile(): string
    {
        return 'platform.systems.files.show';
    }

    public static function accessFileList(): string
    {
        return 'platform.systems.files';
    }

    public static function accessFileAttachments(): string
    {
        return 'platform.systems.files.attach';
    }

    public static function accessFileAssignment(): string
    {
        return 'platform.systems.files.assign';
    }

    public static function accessFileUpdates(): string
    {
        return 'platform.systems.files.update';
    }

    public static function accessFileUploads(): string
    {
        return 'platform.systems.files.upload';
    }

    public function getItemPermission(): ItemPermission
    {
        $item = ItemPermission::group($this->t('group', 'File Explorer'));

        $item->addPermission(static::accessFileList(),       $this->t('list', 'Accessing the file list'));
        $item->addPermission(static::accessViewFile(),        $this->t('show', 'Access to view file'));
        $item->addPermission(static::accessFileAttachments(), $this->t('assign', 'Accessing a file assignment'));
        $item->addPermission(static::accessFileAssignment(), $this->t('attach', 'Access to file attachments'));
        $item->addPermission(static::accessFileUpdates(), $this->t('update', 'Access to file updates'));
        $item->addPermission(static::accessFileUploads(),  $this->t('upload', 'Access to file uploads'));

        return $item;
    }

    private function t(string $key, string $default): string
    {
        return $this->translator->get(Arr::get($this->data, $key, $default));
    }
}
