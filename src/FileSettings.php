<?php

namespace Khamsolt\Orchid\Files;

use Illuminate\Database\Eloquent\Model;
use Khamsolt\Orchid\Files\Contracts\Configuration;
use Khamsolt\Orchid\Files\Models\Attachment;
use Orchid\Support\Presenter;

final class FileSettings
{
    public function __construct(
        private readonly Configuration $configuration,
    ) {
    }

    public function resolveUserPresenter(Attachment $attachment): Presenter|int|string
    {
        $className = $this->configuration->user('presenter');

        $model = $attachment->getRelation('user');

        if (
            class_exists($className) &&
            $model instanceof Model &&
            ($presenter = new $className($model)) instanceof Presenter) {
            return $presenter;
        }

        /** @var int $id */
        $id = $model->getKey();

        return $id;
    }
}
