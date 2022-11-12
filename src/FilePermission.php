<?php

namespace Khamsolt\Orchid\Files;

use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Translation\Translator;
use Illuminate\Support\Arr;
use Khamsolt\Orchid\Files\Contracts\Permissions;
use Orchid\Platform\ItemPermission;

class FilePermission implements Permissions
{
    private readonly array $settings;

    private array $permissionTitles;

    private iterable $permissionKeys;

    public function __construct(
        private readonly Repository $config,
        private readonly Translator $translator
    ) {
        /** @var array<string, array<string, mixed>> $settings */
        $settings = $this->config->get('orchid-files.permissions');

        $this->settings = $settings;

        $this->permissionKeys = $this->resolvePermissionKeys();

        $this->permissionTitles = $this->resolvePermissionTitles();
    }

    private function resolvePermissionKeys(): array
    {
        /** @var array<string, iterable> $value */
        $value = $this->settings['keys'] ?? [];

        return $value;
    }

    private function resolvePermissionTitles(): array
    {
        /** @var array<string, string> $value */
        $value = $this->settings['titles'] ?? [];

        return $value;
    }

    public function getItemPermission(): ItemPermission
    {
        $item = ItemPermission::group($this->getTitleGroup());

        $item->addPermission($this->firstValue($this->accessFileList()), $this->getTitleList());
        $item->addPermission($this->firstValue($this->accessViewFile()), $this->getTitleView());
        $item->addPermission($this->firstValue($this->accessFileAttachments()), $this->getTitleAssign());
        $item->addPermission($this->firstValue($this->accessFileAssignment()), $this->getTitleAttach());
        $item->addPermission($this->firstValue($this->accessFileUpdates()), $this->getTitleUpdate());
        $item->addPermission($this->firstValue($this->accessFileUploads()), $this->getTitleUpload());

        return $item;
    }

    private function getTitleGroup(): string
    {
        return $this->translate('group', 'File Explorer');
    }

    private function translate(string $key, string $default): string
    {
        /** @var string $title */
        $title = $this->translator->get($this->permissionTitles[$key] ?? $default);

        return $title;
    }

    private function firstValue(iterable $data): string
    {
        /** @var string $value */
        $value = Arr::first($data);

        return $value;
    }

    public function accessFileList(): iterable
    {
        return $this->permissionKeys['list'] ?? ['platform.files.list'];
    }

    private function getTitleList(): string
    {
        return $this->translate('list', 'Accessing the file list');
    }

    public function accessViewFile(): iterable
    {
        return $this->permissionKeys['view'] ?? ['platform.files.view'];
    }

    private function getTitleView(): string
    {
        return $this->translate('view', 'Access to view file');
    }

    public function accessFileAttachments(): iterable
    {
        return $this->permissionKeys['attach'] ?? ['platform.files.assign'];
    }

    private function getTitleAssign(): string
    {
        return $this->translate('assign', 'Accessing a file assignment');
    }

    public function accessFileAssignment(): iterable
    {
        return $this->permissionKeys['assign'] ?? ['platform.files.attach'];
    }

    private function getTitleAttach(): string
    {
        return $this->translate('attach', 'Access to file attachments');
    }

    public function accessFileUpdates(): iterable
    {
        return $this->permissionKeys['update'] ?? ['platform.files.update'];
    }

    private function getTitleUpdate(): string
    {
        return $this->translate('update', 'Access to file updates');
    }

    public function accessFileUploads(): iterable
    {
        return $this->permissionKeys['upload'] ?? ['platform.files.upload'];
    }

    private function getTitleUpload(): string
    {
        return $this->translate('upload', 'Access to file uploads');
    }
}
