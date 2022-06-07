<?php

namespace Khamsolt\Orchid\Files\View\Components;

use Exception;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Thumbnail extends Component
{
    public string $name;

    public string $url;

    public ?string $alt;

    public function __construct(string $name, string $url, ?string $alt = null)
    {
        $this->name = $name;
        $this->url = $url;
        $this->alt = $alt;
    }

    public function render(): View
    {
        return view('orchid-files::components.thumbnail');
    }

    public function __toString(): string
    {
        $view = $this->resolveView();

        if ($view instanceof View) {
            return $view->with($this->data())->render();
        }

        throw new Exception('Thumbnail Component Exception');
    }
}
