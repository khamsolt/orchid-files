<?php

namespace Khamsolt\Orchid\Files\Screens;


use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Translation\Translator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Validation\ValidationException;
use Khamsolt\Orchid\Files\Contracts\Permissions;
use Khamsolt\Orchid\Files\Contracts\Repository as FileRepository;
use Khamsolt\Orchid\Files\Http\Requests\SelectRequest;
use Khamsolt\Orchid\Files\Layouts\FileListLayout;
use Khamsolt\Orchid\Files\Models\Attachment;
use Orchid\Alert\Toast;
use Orchid\Platform\Models\User;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Fields\Cropper;
use Orchid\Screen\Fields\Upload;
use Orchid\Screen\LayoutFactory;
use Orchid\Screen\Screen;
use Orchid\Support\Color;

class FileListScreen extends Screen
{
    public User $user;

    public ?string $mode;

    public ?string $redirect;

    public function __construct(
        private readonly Repository     $config,
        private readonly Permissions    $permissible,
        private readonly FileRepository $fileRepository,
        private readonly Redirector     $redirector,
        private readonly Translator     $translator,
        private readonly Toast          $toast
    ) {
    }

    public function query(Request $request): iterable
    {
        $user = $request->user();

        $files = $this->fileRepository->paginate();

        return [
            'files' => $files,
            'user' => $user,
            'config' => $this->config,
            'permissible' => $this->permissible,
            'mode' => $request->get('mode'),
            'redirect' => $request->get('redirect'),
            'router' => $this->redirector->getUrlGenerator()
        ];
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
        return $this->permissible->accessFileList();
    }

    public function commandBar(): iterable
    {
        /** @var string $uploadRoute */
        $uploadRoute = $this->config->get('orchid-files.routes.upload', 'platform.files.upload');

        return [
            Link::make('Upload File')
                ->type(new Color('default'))
                ->icon('add')
                ->route($uploadRoute)
                ->canSee(!$this->redirect && !$this->mode),

            Button::make('Attach')
                ->type(new Color('primary'))
                ->icon('check')
                ->method('attaching', [
                    'url' => $this->redirect
                ])
                ->canSee($this->redirect && $this->mode),
        ];
    }

    public function layout(): iterable
    {
        return [
            LayoutFactory::rows([
                Upload::make('upload.files')->title('Quick Loading'),

                Cropper::make('upload.image')
                    ->staticBackdrop()
                    ->title('Image')
            ])->canSee($this->user->hasAnyAccess($this->permissible->accessFileUploads())),

            LayoutFactory::blank([
                new FileListLayout($this->redirector->getUrlGenerator()),
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

    public function delete(int $attachmentId): void
    {
        $attachment = Attachment::query()->where('id', '=', $attachmentId)->firstOrFail();

        $attachment->delete();

        /** @var string $successMessage */
        $successMessage = $this->translator->get('Media file deleted successfully');

        $this->toast->success($successMessage);
    }
}
