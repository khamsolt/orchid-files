<?php

namespace Khamsolt\Orchid\Files\Screens;

use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Khamsolt\Orchid\Files\Contracts\Authorization;
use Khamsolt\Orchid\Files\Contracts\Configuration;
use Khamsolt\Orchid\Files\Contracts\Translation;
use Khamsolt\Orchid\Files\Enums\Action;
use Khamsolt\Orchid\Files\FileSettings;
use Khamsolt\Orchid\Files\Models\Attachment;
use Khamsolt\Orchid\Files\View\Components\Preview;
use Orchid\Alert\Toast;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\LayoutFactory;
use Orchid\Screen\Layouts\Persona;
use Orchid\Screen\Screen;
use Orchid\Screen\Sight;
use Orchid\Support\Presenter;
use Throwable;

final class FileViewScreen extends Screen
{
    public int|null $id = null;

    public string|null $url = null;

    public bool $isImage = false;

    public function __construct(
        private readonly Authorization $authorization,
        private readonly Configuration $configuration,
        private readonly Translation $translator,
        private readonly Redirector $redirector,
        private readonly Toast $toast
    ) {
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
        return $this->authorization->authorize(Action::VIEW);
    }

    public function query(Attachment $attachment): array
    {
        $attachment->load(['user']);

        return [
            'attachment' => $attachment,
            'id' => $attachment->getKey(),
            'alt' => $attachment->getAttribute('alt') ?? $attachment->getAttribute('original_name'),
            'url' => $attachment->url(),
            'isImage' => $attachment->isImage(),
        ];
    }

    public function commandBar(): array
    {
        return [
            Button::make('Delete')
                ->confirm($this->translator->get('Once the media file is deleted, all of its resources and data will be permanently deleted. Before deleting your media file, please download any data or information that you wish to retain.'))
                ->icon('bs.file-minus-fill')
                ->method('delete'),

            Link::make('Open')
                ->target('blank')
                ->icon('bs.file-arrow-down-fill')
                ->href($this->url),

            Link::make('Edit')
                ->target('blank')
                ->icon('bs.file-break-fill')
                ->route($this->configuration->route(Action::EDIT), [$this->id]),
        ];
    }

    public function layout(): iterable
    {
        return [
            LayoutFactory::component(Preview::class)
                ->canSee($this->isImage),

            LayoutFactory::legend('attachment', [
                Sight::make('id', $this->translator->get('#ID')),

                Sight::make('user_id', $this->translator->get('User'))
                    ->render(function (Attachment $attachment) {
                        if ($attachment->getRelation('user') === null) {
                            return null;
                        }

                        $filePresenter = new FileSettings($this->configuration);

                        $presenter = $filePresenter->resolveUserPresenter($attachment);

                        if ($presenter instanceof Presenter) {
                            return new Persona($presenter);
                        }

                        return $this->translator->get('User ID :id', ['id' => $presenter]);
                    }),

                Sight::make('name', $this->translator->get('Name')),
                Sight::make('original_name', $this->translator->get('Title')),
                Sight::make('mime', $this->translator->get('Mime')),
                Sight::make('extension', $this->translator->get('Extension')),

                Sight::make('size', $this->translator->get('Size'))
                    ->render(fn(Attachment $attachment) => $attachment->sizeToKb().' Kb'),

                Sight::make('sort', $this->translator->get('Sort')),
                Sight::make('path', $this->translator->get('Path')),
                Sight::make('description', $this->translator->get('Description')),
                Sight::make('alt', $this->translator->get('Alt')),
                Sight::make('hash', $this->translator->get('Hash')),
                Sight::make('disk', $this->translator->get('Disk')),
                Sight::make('group', $this->translator->get('Group')),

                Sight::make('created_at', $this->translator->get('Created'))
                    ->render(fn(Attachment $attachment) => $attachment->created_at->toDateTimeString()),

                Sight::make('updated_at', $this->translator->get('Updated'))
                    ->render(fn(Attachment $attachment) => $attachment->updated_at->toDateTimeString()),
            ]),
        ];
    }

    /**
     * @throws Throwable
     */
    public function delete(Attachment $attachment): RedirectResponse
    {
        $attachment->deleteOrFail();

        $this->toast->success($this->translator->get('Media file deleted successfully'));

        return $this->redirector->route($this->configuration->route(Action::LIST));
    }
}
