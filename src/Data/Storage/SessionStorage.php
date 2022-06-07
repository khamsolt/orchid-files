<?php

namespace Khamsolt\Orchid\Files\Data\Storage;

use Illuminate\Contracts\Session\Session;
use Khamsolt\Orchid\Files\Contracts\Storage;

class SessionStorage implements Storage
{
    private Session $session;

    public function __construct(Session $session)
    {
        $this->session = $session;
    }

    public function put(string $key, array $data): void
    {
        $this->session->put($key, $data);
    }

    public function forget(string $key): void
    {
        $this->session->forget($key);
    }

    public function pull(string $key): ?array
    {
        return $this->session->pull($key);
    }

    public function has(string $key): bool
    {
        return $this->session->has($key);
    }
}
