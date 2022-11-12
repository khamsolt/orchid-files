<?php

namespace Khamsolt\Orchid\Files\Presenters;

use Khamsolt\Orchid\Files\Models\Attachment;
use Orchid\Screen\Contracts\Personable;
use Orchid\Support\Presenter;

/**
 * @property-read Attachment $entity
 */
class FilePresenter extends Presenter implements Personable
{
    public function title(): string
    {
        return $this->entity->original_name;
    }

    public function subTitle(): string
    {
        return $this->entity->hash;
    }

    public function url(): string
    {
        /** @var string $viewUrl */
        $viewUrl = config('orchid-files.routes.view');

        return route($viewUrl, [
            'attachment' => $this->entity->id,
        ]);
    }

    public function image(): ?string
    {
        return $this->entity->thumbnail();
    }
}
