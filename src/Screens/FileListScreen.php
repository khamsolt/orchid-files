<?php

namespace Khamsolt\Orchid\Files\Screens;


use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Translation\Translator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Khamsolt\Orchid\Files\Contracts\Assignable;
use Khamsolt\Orchid\Files\Contracts\Attachable;
use Khamsolt\Orchid\Files\Contracts\Entities\Permissible;
use Khamsolt\Orchid\Files\Contracts\Searchable;
use Khamsolt\Orchid\Files\Exceptions\AttachedFileException;
use Khamsolt\Orchid\Files\Http\Requests\SelectRequest;
use Khamsolt\Orchid\Files\Layouts\FileListLayout;
use Orchid\Alert\Toast;
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

    public string|int|null $key;

    public function __construct(
        private readonly LayoutFactory $layoutFactory,
        private readonly Repository $config,
        private readonly Permissible $permissible,
        private readonly Searchable $searchService,
        private readonly Attachable $attachService,
        private readonly Assignable $selectManager,
        private readonly Redirector $redirector,
        private readonly Translator $translator,
        private readonly Toast $toast
    )
    {
    }

    public function query(Request $request): iterable
    {
        $user = $request->user();

        $mode = $request->get('mode');

        $key = $request->get('key');

        $files = $this->searchService->paginate();

        return [
            'files'  => $files,
            'mode'   => $mode,
            'key'    => $key,
            'user'   => $user,
            'config' => $this->config,
            'permissible' => $this->permissible
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
            Button::make($this->translator->get('Select'))
                ->type(new Color('default'))
                ->icon('check')
                ->method('attach', [
                    'id' => $this->key
                ])
                ->canSee($this->selectManager->has($this->key)),
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

    public function attach(SelectRequest $request): RedirectResponse
    {
        $attachmentId = $request->getFirst();

        $key  = $request->get('id');

        try {
            $dto = $this->selectManager->retrieve($key);

            $this->attachService->attach((int)$attachmentId, $dto->type, (int)$dto->id, $dto->group);

            $this->toast->success($this->translator->get('The file has been successfully attached'));

            return $this->redirector->route($dto->redirect, $dto->id);
        } catch (AttachedFileException $exception) {
            $this->toast->error($this->translator->get('Attempting to attach a file failed, contact the administrator'));
        }

        return $this->redirector->route($this->config->get('orchid-files.routes.main'));
    }
}
