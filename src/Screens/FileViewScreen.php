<?php

namespace Khamsolt\Orchid\Files\Screens;

use Illuminate\Contracts\Config\Repository;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Khamsolt\Orchid\Files\Contracts\Entities\Permissible;
use Khamsolt\Orchid\Files\Models\Attachment;
use Khamsolt\Orchid\Files\View\Components\Preview;
use Orchid\Alert\Toast;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\LayoutFactory;
use Orchid\Screen\Layouts\Persona;
use Orchid\Screen\Screen;
use Orchid\Screen\Sight;

class FileViewScreen extends Screen
{
    public int $id;

    public string $url;

    public bool $isImage = false;

    public function __construct(private readonly LayoutFactory $layoutFactory,
                                private readonly Repository $config,
                                private readonly Permissible $permissible,
                                private readonly Redirector $redirector,
                                private readonly Toast $toast)
    {
    }

    public function name(): ?string
    {
        return 'File';
    }

    public function description(): ?string
    {
        return 'On this page you can get detailed information about the selected file';
    }

    public function permission(): ?iterable
    {
        return $this->permissible->accessViewFile();
    }

    public function query(Attachment $attachment): array
    {
        return [
            'attachment' => $attachment,
            'id'         => $attachment->getKey(),
            'alt'        => $attachment->getAttribute('alt') ?? $attachment->getAttribute('original_name'),
            'url'        => $attachment->url(),
            'isImage'    => $attachment->isImage()
        ];
    }

    public function commandBar(): array
    {
        return [
            Button::make('Delete')
                ->confirm(__('Once the media file is deleted, all of its resources and data will be permanently deleted. Before deleting your media file, please download any data or information that you wish to retain.'))
                ->icon('trash')
                ->method('delete'),

            Link::make('Open')
                ->icon('cloud-download')
                ->href($this->url),

            Link::make('Edit')
                ->icon('pencil')
                ->route($this->config->get('orchid-files.routes.edit'), $this->id),
        ];
    }

    public function layout(): iterable
    {
        return [
            $this->layoutFactory->component(Preview::class)
                ->canSee($this->isImage),

            $this->layoutFactory->legend('attachment', [
                Sight::make('id', __('#ID')),

                Sight::make('user_id', __('User'))
                    ->render(fn (Attachment $attachment) => (string)(new Persona($attachment->user->presenter()))),

                Sight::make('name', __('Name')),
                Sight::make('original_name', __('Title')),
                Sight::make('mime', __('Mime')),
                Sight::make('extension', __('Extension')),

                Sight::make('size', __('Size'))
                    ->render(fn (Attachment $attachment) => $attachment->sizeToKb() . ' Kb'),

                Sight::make('sort', __('Sort')),
                Sight::make('path', __('Path')),
                Sight::make('description', __('Description')),
                Sight::make('alt', __('Alt')),
                Sight::make('hash', __('Hash')),
                Sight::make('disk', __('Disk')),
                Sight::make('group', __('Group')),

                Sight::make('created_at', __('Created'))
                    ->render(fn (Attachment $attachment) => (string)$attachment->getAttribute('created_at')?->toDateTimeString()),

                Sight::make('updated_at', __('Updated'))
                    ->render(fn (Attachment $attachment) => (string)$attachment->getAttribute('updated_at')?->toDateTimeString()),
            ]),
        ];
    }

    public function delete(Attachment $attachment): RedirectResponse
    {
        $attachment->delete();

        $this->toast->success(__('Media file deleted successfully'));

        return $this->redirector->route($this->config->get('orchid-files.routes.list'));
    }
}
