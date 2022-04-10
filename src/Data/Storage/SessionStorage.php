<?php

namespace Khamsolt\Orchid\Files\Data\Storage;

use Illuminate\Contracts\Session\Session;
use Khamsolt\Orchid\Files\Contracts\Data\TransferObject;
use Khamsolt\Orchid\Files\Contracts\Storage;
use Khamsolt\Orchid\Files\Data\Transfer\AssignmentObject;

class SessionStorage implements Storage
{
    private Session $session;

    public function __construct(Session $session)
    {
        $this->session = $session;
    }

    public function put(string $key, TransferObject $object): void
    {
        $this->session->put($key, $object);
    }

    public function forget(string $key): void
    {
        $this->session->forget($key);
    }

    public function pull(string $key): null|TransferObject|AssignmentObject
    {
        return $this->session->pull($key);
    }

    public function has(string $key): bool
    {
        return $this->session->has($key);
    }
}
