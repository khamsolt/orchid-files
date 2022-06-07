<?php

namespace Khamsolt\Orchid\Files;

use Illuminate\Contracts\Config\Repository;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Khamsolt\Orchid\Files\Contracts\Assignable;
use Khamsolt\Orchid\Files\Contracts\Storage;
use Khamsolt\Orchid\Files\Data\Transfer\AssignmentObject;
use Khamsolt\Orchid\Files\Enums\Mode;

class FileAssigment implements Assignable
{
    public function __construct(private readonly Storage    $storage,
                                private readonly Redirector $redirector,
                                private readonly Repository $config)
    {
    }

    public function putWithRedirect(AssignmentObject $attachmentableObject, Mode $mode = Mode::SINGLE): RedirectResponse
    {
        $route = $this->config->get('orchid-files.routes.list');

        $redirect = $this->redirector->route($route, ['mode' => $mode]);

        $key = sprintf('%s:%s', static::KEY_FILE_ASSIGNMENT, $attachmentableObject->id);

        $this->storage->forget($key);

        $this->storage->put($key, $attachmentableObject->toArray());

        return $redirect;
    }

    public function retrieve(string $key): AssignmentObject
    {
        $data = $this->storage->pull(static::KEY_FILE_ASSIGNMENT . ":$key");

        return new AssignmentObject($data);
    }

    public function has(int|string|null $key): bool
    {
        return $this->storage->has(static::KEY_FILE_ASSIGNMENT . ":$key");
    }
}
