<?php

namespace Khamsolt\Orchid\Files\Listeners;

use Illuminate\Http\RedirectResponse;
use Khamsolt\Orchid\Files\Contracts\Assignable;
use Khamsolt\Orchid\Files\Data\Transfer\AssignmentObject;
use Khamsolt\Orchid\Files\Events\AttachedFile;

class AttachedFileListener
{
    public function __construct(private readonly Assignable $assignable)
    {
    }

    public function handle(AttachedFile $event): RedirectResponse
    {
        $dto = new AssignmentObject($event->toArray());

        return $this->assignable->putWithRedirect($dto);
    }
}
