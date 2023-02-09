<?php

namespace Khamsolt\Orchid\Files\Screens;

use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Translation\Translator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Khamsolt\Orchid\Files\Contracts\Permissions;
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

class FileViewScreen extends Screen
{
    public int $id;

    public string $url;

    public bool $isImage = false;

    public function __construct(
        private readonly Repository  $config,
        private readonly Permissions $permissible,
        private readonly Redirector  $redirector,
        private readonly Translator  $translator,
        private readonly Toast       $toast
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
        return $this->permissible->accessViewFile();
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
        /** @var string $deleteConfirmMessage */
        $deleteConfirmMessage = $this->translator->get('Once the media file is deleted, all of its resources and data will be permanently deleted. Before deleting your media file, please download any data or information that you wish to retain.');

        /** @var string $urlView */
        $urlView = $this->config->get('orchid-files.routes.edit');

        return [
            Button::make('Delete')
                ->confirm($deleteConfirmMessage)
                ->icon('trash')
                ->method('delete'),

            Link::make('Open')
                ->target('blank')
                ->icon('cloud-download')
                ->href($this->url),

            Link::make('Edit')
                ->target('blank')
                ->icon('pencil')
                ->route($urlView, [$this->id]),
        ];
    }

    public function layout(): iterable
    {
        return [
            LayoutFactory::component(Preview::class)
                ->canSee($this->isImage),

            LayoutFactory::legend('attachment', [
                Sight::make('id', $this->translate('#ID')),

                Sight::make('user_id', $this->translate('User'))
                    ->render(function (Attachment $attachment) {
                        if ($attachment->getRelation('user') === null) {
                            return null;
                        }
                        /** @var array<string, class-string> $presenters */
                        $presenters = $this->config->get('orchid-files.presenters');

                        $filePresenter = new FileSettings();

                        $presenter = $filePresenter->resolveUserPresenter($attachment, $presenters);

                        if ($presenter instanceof Presenter) {
                            return new Persona($presenter);
                        }

                        return $this->translate('User ID :id', ['id' => $presenter]);
                    }),

                Sight::make('name', $this->translate('Name')),
                Sight::make('original_name', $this->translate('Title')),
                Sight::make('mime', $this->translate('Mime')),
                Sight::make('extension', $this->translate('Extension')),

                Sight::make('size', $this->translate('Size'))
                    ->render(fn (Attachment $attachment) => $attachment->sizeToKb() . ' Kb'),

                Sight::make('sort', $this->translate('Sort')),
                Sight::make('path', $this->translate('Path')),
                Sight::make('description', $this->translate('Description')),
                Sight::make('alt', $this->translate('Alt')),
                Sight::make('hash', $this->translate('Hash')),
                Sight::make('disk', $this->translate('Disk')),
                Sight::make('group', $this->translate('Group')),

                Sight::make('created_at', $this->translate('Created'))
                    ->render(fn (Attachment $attachment) => $attachment->created_at->toDateTimeString()),

                Sight::make('updated_at', $this->translate('Updated'))
                    ->render(fn (Attachment $attachment) => $attachment->updated_at->toDateTimeString()),
            ]),
        ];
    }

    public function delete(Attachment $attachment): RedirectResponse
    {
        $attachment->deleteOrFail();

        /** @var string $successMessage */
        $successMessage = $this->translator->get('Media file deleted successfully');

        $this->toast->success($successMessage);

        /** @var string $redirectListUrl */
        $redirectListUrl = $this->config->get('orchid-files.routes.list');

        return $this->redirector->route($redirectListUrl);
    }

    /**
     * @param string $text
     * @param array<string, string|int|float> $replace
     * @return string
     */
    private function translate(string $text, array $replace = []): string
    {
        /** @var string $translated */
        $translated = $this->translator->get($text, $replace);

        return $translated;
    }
}
