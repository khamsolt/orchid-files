<?php

namespace Khamsolt\Orchid\Files\Authorization;

use Illuminate\Config\Repository;
use Illuminate\Contracts\Translation\Translator;
use Illuminate\Support\Arr;
use Khamsolt\Orchid\Files\Contracts\Entities\Permissible;
use Orchid\Platform\ItemPermission;

class Permissions implements Permissible
{
    public function __construct(private readonly Repository $config,
                                private readonly Translator $translator)
    {
    }

    public function accessViewFile(): ?iterable
    {
        return $this->config->get('orchid-files.permissions.keys.view');
    }

    public function accessFileList(): ?iterable
    {
        return $this->config->get('orchid-files.permissions.keys.list');
    }

    public function accessFileAttachments(): ?iterable
    {
        return $this->config->get('orchid-files.permissions.keys.attach');
    }

    public function accessFileAssignment(): ?iterable
    {
        return $this->config->get('orchid-files.permissions.keys.assign');
    }

    public function accessFileUpdates(): ?iterable
    {
        return $this->config->get('orchid-files.permissions.keys.update');
    }

    public function accessFileUploads(): ?iterable
    {
        return $this->config->get('orchid-files.permissions.keys.upload');
    }

    public function getItemPermission(): ItemPermission
    {
        $data = $this->config->get('orchid-files.permissions.titles', []);

        $item = ItemPermission::group($this->t('group', 'File Explorer', $data));

        $item->addPermission(Arr::first(static::accessFileList()), $this->t('list', 'Accessing the file list', $data));
        $item->addPermission(Arr::first(static::accessViewFile()), $this->t('view', 'Access to view file', $data));
        $item->addPermission(Arr::first(static::accessFileAttachments()), $this->t('assign', 'Accessing a file assignment', $data));
        $item->addPermission(Arr::first(static::accessFileAssignment()), $this->t('attach', 'Access to file attachments', $data));
        $item->addPermission(Arr::first(static::accessFileUpdates()), $this->t('update', 'Access to file updates', $data));
        $item->addPermission(Arr::first(static::accessFileUploads()), $this->t('upload', 'Access to file uploads', $data));

        return $item;
    }

    private function t(string $key, string $default, array $data): string
    {
        return $this->translator->get(Arr::get($data, $key, $default));
    }
}
