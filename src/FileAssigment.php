<?php

namespace Khamsolt\Orchid\Files;

use Illuminate\Contracts\Config\Repository;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Khamsolt\Orchid\Files\Contracts\Assignable;
use Khamsolt\Orchid\Files\Contracts\Data\TransferObject;
use Khamsolt\Orchid\Files\Contracts\Storage;
use Khamsolt\Orchid\Files\Data\Transfer\AssignmentObject;
use Khamsolt\Orchid\Files\Exceptions\AttachedFileException;

class FileAssigment implements Assignable
{
    private Storage $storage;

    private Redirector $redirector;

    private Repository $config;

    public function __construct(Storage $storage, Redirector $redirector, Repository $config)
    {
        $this->storage = $storage;
        $this->redirector = $redirector;
        $this->config = $config;
    }

    public function putWithRedirect(AssignmentObject $attachmentableObject): RedirectResponse
    {
        $route = $this->config->get('orchid-files.route-names.files', 'platform.systems.files');

        $redirect = $this->redirector->route($route, ['mode' => 'radio']);

        $this->storage->forget(static::KEY_FILE_ASSIGNMENT);
        ;

        $this->storage->put(static::KEY_FILE_ASSIGNMENT, $attachmentableObject);

        return $redirect;
    }

    public function retrieve(string $key = self::KEY_FILE_ASSIGNMENT): TransferObject|AssignmentObject
    {
        $dto = $this->storage->pull($key);

        if ($dto instanceof AssignmentObject) {
            return $dto;
        }

        throw new AttachedFileException();
    }

    public function has(): bool
    {
        return $this->storage->has(static::KEY_FILE_ASSIGNMENT);
    }
}
