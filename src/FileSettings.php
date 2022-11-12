<?php

namespace Khamsolt\Orchid\Files;

use Illuminate\Database\Eloquent\Model;
use Khamsolt\Orchid\Files\Models\Attachment;
use Orchid\Support\Presenter;

class FileSettings
{
    public function resolveUserPresenter(Attachment $attachment, array $presenters): Presenter|int
    {
        /** @var class-string $className */
        $className = $presenters['user'] ?? null;

        /** @var Model $user */
        $user = $attachment->getRelation('user');

        if (class_exists($className)) {

            /** @var Presenter $presenter */
            $presenter = new $className($user);

            return $presenter;
        }

        /** @var int $id */
        $id = $user->getKey();

        return $id;
    }
}
