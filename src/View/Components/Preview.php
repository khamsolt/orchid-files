<?php

namespace Khamsolt\Orchid\Files\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Preview extends Component
{
    public string $url;

    public string $alt;

    public function __construct(string $url, string $alt)
    {
        $this->url = $url;
        $this->alt = $alt;
    }

    public function render(): View
    {
        return view('orchid-files::components.preview');
    }
}
