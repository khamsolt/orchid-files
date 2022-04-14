<?php

namespace Khamsolt\Orchid\Files\Screens;

use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Khamsolt\Orchid\Files\Authorization\Permissions;
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
    private int $id;

    private string $url;

    private bool $isImage = false;

    private LayoutFactory $layoutFactory;

    private Redirector $redirector;

    private Toast $toast;

    public function __construct(LayoutFactory $layoutFactory, Redirector $redirector, Toast $toast)
    {
        $this->layoutFactory = $layoutFactory;
        $this->redirector = $redirector;
        $this->toast = $toast;

        $this->permission = Permissions::accessViewFile();
    }

    public function name(): ?string
    {
        return 'File';
    }

    public function description(): ?string
    {
        return 'On this page you can get detailed information about the selected file';
    }

    public function query(Attachment $attachment): array
    {
        $this->id = $attachment->id;
        $this->url = $attachment->url();
        $this->isImage = $attachment->isImage();

        return [
            'attachment' => $attachment,
            'alt' => $attachment->alt ?? $attachment->original_name,
            'url' => $this->url,
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
                ->route('platform.systems.files.edit', $this->id),
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
                    ->render(fn (Attachment $attachment) => (string)$attachment->created_at?->toDateTimeString()),
                Sight::make('updated_at', __('Updated'))
                    ->render(fn (Attachment $attachment) => (string)$attachment->updated_at?->toDateTimeString()),
            ]),
        ];
    }

    public function delete(Attachment $attachment): RedirectResponse
    {
        $attachment->delete();

        $this->toast->success(__('Media file deleted successfully'));

        return $this->redirector->route('platform.systems.users');
    }
}
