<?php

namespace Khamsolt\Orchid\Files\View\Components;

use Exception;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use Khamsolt\Orchid\Files\Models\Attachment;

class Thumbnail extends Component
{
    public string $title;

    public string $url;

    public string $subTitle;

    public string $image;

    public function __construct(Attachment $attachment, string $url)
    {
        $this->title = $attachment->original_name;
        $this->image = $attachment->thumbnail();
        $this->subTitle = $attachment->hash ?? '-';
        $this->url = $url;
    }

    public function __toString(): string
    {
        $view = $this->resolveView();

        if ($view instanceof View) {
            return $view->with($this->data())->render();
        }

        throw new Exception('Thumbnail Component Exception');
    }

    public function render(): View
    {
        return view('orchid-files::components.thumbnail');
    }
}
