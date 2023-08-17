<?php

namespace Khamsolt\Orchid\Files;

use Khamsolt\Orchid\Files\Contracts\Authorization;
use Khamsolt\Orchid\Files\Contracts\Configuration;
use Khamsolt\Orchid\Files\Contracts\Translation;
use Khamsolt\Orchid\Files\Enums\Action;
use Orchid\Platform\ItemPermission;


final class FileAuthorize implements Authorization
{
    public function __construct(
        private readonly Configuration $configuration,
        private readonly Translation $translator
    ) {
    }

    public function authorize(Action $action): array
    {
        $slug = $this->permissionSlug($action);

        $accesses = $this->configuration->permissionAccesses($action);

        return [
            $slug,
            ...$accesses
        ];
    }

    private function permissionSlug(Action $action): string
    {
        return $this->configuration->permissionKey($action);
    }

    public function getItemPermission(): ItemPermission
    {
        $item = ItemPermission::group($this->getTitleGroup());

        $this->permissionAssign($item, Action::LIST);
        $this->permissionAssign($item, Action::VIEW);
        $this->permissionAssign($item, Action::EDIT);
        $this->permissionAssign($item, Action::DELETE);
        $this->permissionAssign($item, Action::ATTACH);
        $this->permissionAssign($item, Action::ASSIGN);
        $this->permissionAssign($item, Action::UPDATE);
        $this->permissionAssign($item, Action::UPLOAD);

        return $item;
    }

    private function getTitleGroup(): string
    {
        return $this->translator->get($this->configuration->name());
    }

    private function permissionAssign(ItemPermission $permission, Action $action): void
    {
        $permission->addPermission($this->permissionSlug($action), $this->permissionTitle($action));
    }

    private function permissionTitle(Action $action): string
    {
        return $this->translator->get($this->configuration->permissionTitle($action));
    }
}
