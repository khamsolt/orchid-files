<?php

namespace Khamsolt\Orchid\Files\Screens;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Validation\Rule;
use Khamsolt\Orchid\Files\Contracts\Authorization;
use Khamsolt\Orchid\Files\Contracts\Configuration;
use Khamsolt\Orchid\Files\Contracts\Repository as FileRepository;
use Khamsolt\Orchid\Files\Contracts\Translation;
use Khamsolt\Orchid\Files\Enums\Action;
use Khamsolt\Orchid\Files\Enums\Mode;
use Khamsolt\Orchid\Files\Enums\Type;
use Khamsolt\Orchid\Files\Http\Requests\SelectRequest;
use Khamsolt\Orchid\Files\Layouts\FileListLayout;
use Khamsolt\Orchid\Files\Models\Attachment;
use Khamsolt\Orchid\Files\Orchid\Actions\Button as CKEditorButtonHandler;
use Orchid\Alert\Toast;
use Orchid\Platform\Models\User;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Fields\Cropper;
use Orchid\Screen\Fields\Upload;
use Orchid\Screen\LayoutFactory;
use Orchid\Screen\Screen;
use Orchid\Support\Color;

final class FileListScreen extends Screen
{
    public User|null $user = null;

    public Type|null $type = null;

    public string|null $mode = null;

    public string|null $redirect = null;

    public function __construct(
        private readonly Request $request,
        private readonly Configuration $configuration,
        private readonly Authorization $authorization,
        private readonly FileRepository $fileRepository,
        private readonly Redirector $redirector,
        private readonly Translation $translator,
        private readonly Toast $toast
    ) {
    }

    public function name(): ?string
    {
        return 'Files';
    }

    public function description(): ?string
    {
        return 'A list of all your files that you have uploaded through the ORCHID platform';
    }

    public function permission(): ?iterable
    {
        return $this->authorization->authorize(Action::LIST);
    }

    public function commandBar(): iterable
    {
        return [
            Link::make('Upload File')
                ->icon('bs.file-plus-fill')
                ->route($this->configuration->route(Action::UPLOAD))
                ->canSee(!$this->redirect && !$this->mode),

            Button::make('Attach')
                ->icon('bs.file-check-fill')
                ->method('attaching', [
                    'url' => $this->redirect,
                ])
                ->canSee($this->redirect && $this->mode),
        ];
    }

    public function layout(): iterable
    {
        $funcNum = $this->request->get('CKEditorFuncNum');

        return [
            LayoutFactory::rows([
                Upload::make('upload.files')
                    ->title('Quick Loading'),

                Cropper::make('upload.image')
                    ->staticBackdrop()
                    ->title('Image')

            ])->canSee($this->user->hasAnyAccess($this->authorization->authorize(Action::LIST))),

            LayoutFactory::block(new FileListLayout(
                $this->user,
                $this->redirector->getUrlGenerator(),
                $this->authorization,
                $this->configuration
            ))
                ->vertical()
                ->commands([
                    CKEditorButtonHandler::make('Select')
                        ->canSee((bool) $this->type)
                        ->set('data-ckeditor-func-num', $funcNum)
                        ->icon('loop')
                        ->type(Color::PRIMARY),
                ]),
        ];
    }

    public function attaching(SelectRequest $request): RedirectResponse
    {
        $attachmentId = $request->getFirst();

        /** @var string $url */
        $url = $request->get('url');

        $attachment = $this->fileRepository->find($attachmentId);

        $query = http_build_query([
            'attachment_id' => $attachmentId,
            'attachment_url' => $attachment->url(),
        ]);

        return $this->redirector->secure("$url?$query");
    }

    public function delete(Attachment $attachment): void
    {
        $attachment->delete();

        $this->toast->success($this->translator->get('Media file deleted successfully'));
    }

    public function query(Request $request): iterable
    {
        $data = $request->validate([
            'mode' => ['nullable', 'string', Rule::enum(Mode::class)],
            'redirect' => ['nullable', 'string', 'url'],
            'type' => ['nullable', 'string', Rule::enum(Type::class)],
        ]);

        $user = $request->user();

        $files = $this->fileRepository->paginate($data['type'] ?? null);

        return [
            'files' => $files,
            'user' => $user,
            ... $data
        ];
    }

    private function resolveType(Request $request): ?Type
    {
        /** @var string|null $type */
        $type = $request->get('type');

        return is_string($type) ? Type::tryFrom($type) : null;
    }

}
