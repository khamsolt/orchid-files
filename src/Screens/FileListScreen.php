<?php

namespace Khamsolt\Orchid\Files\Screens;


use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Translation\Translator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Khamsolt\Orchid\Files\Contracts\Entities\Permissible;
use Khamsolt\Orchid\Files\Contracts\Searchable;
use Khamsolt\Orchid\Files\Http\Requests\SelectRequest;
use Khamsolt\Orchid\Files\Layouts\FileListLayout;
use Orchid\Platform\Models\User;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\Upload;
use Orchid\Screen\LayoutFactory;
use Orchid\Screen\Screen;
use Orchid\Support\Color;

class FileListScreen extends Screen
{
    public User $user;

    public ?string $mode;

    public ?string $redirect;

    public function __construct(private readonly LayoutFactory $layoutFactory,
                                private readonly Repository $config,
                                private readonly Permissible $permissible,
                                private readonly Searchable $searchService,
                                private readonly Redirector $redirector,
                                private readonly Translator $translator
    )
    {
    }

    public function query(Request $request): iterable
    {
        $user = $request->user();

        $files = $this->searchService->paginate();

        return [
            'files' => $files,
            'user' => $user,
            'config' => $this->config,
            'permissible' => $this->permissible,
            'mode' => $request->get('mode'),
            'redirect' => $request->get('redirect')
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
        return [
            Button::make($this->translator->get('Attach'))
                ->type(new Color('default'))
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
            $this->layoutFactory->rows([

                Upload::make('files_with_catalog')->title('Upload With Catalog'),

            ])->canSee($this->user->hasAnyAccess($this->permissible->accessFileUploads())),

            $this->layoutFactory->blank([
                FileListLayout::class,
            ]),
        ];
    }

    public function attaching(SelectRequest $request): RedirectResponse
    {
        $attachmentId = $request->getFirst();

        $url = $request->get('url');

        $attachment = $this->searchService->find($attachmentId);

        $query = http_build_query([
            'attachment_id' => $attachmentId,
            'attachment_url' => $attachment->url(),
        ]);

        return $this->redirector->secure("$url?$query");
    }
}
