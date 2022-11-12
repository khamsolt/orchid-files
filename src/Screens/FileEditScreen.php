<?php

namespace Khamsolt\Orchid\Files\Screens;

use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Translation\Translator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Routing\Redirector;
use Khamsolt\Orchid\Files\Contracts\Permissions;
use Khamsolt\Orchid\Files\Contracts\Updatable;
use Khamsolt\Orchid\Files\Contracts\Uploadable;
use Khamsolt\Orchid\Files\Http\Requests\UpdateRequest;
use Khamsolt\Orchid\Files\Http\Requests\UploadRequest;
use Khamsolt\Orchid\Files\Layouts\FileEditLayout;
use Khamsolt\Orchid\Files\Models\Attachment;
use Khamsolt\Orchid\Files\View\Components\Preview;
use Orchid\Alert\Toast;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\LayoutFactory;
use Orchid\Screen\Screen;
use Orchid\Support\Color;

class FileEditScreen extends Screen
{
    public bool $exists = true;
    public bool $isImage = false;

    public function __construct(
        private readonly Permissions $permissible,
        private readonly Repository  $config,
        private readonly Updatable   $updateService,
        private readonly Uploadable  $uploadService,
        private readonly Redirector  $redirector,
        private readonly Translator  $translator,
        private readonly Toast       $toast
    ) {
    }

    public function query(Attachment $attachment): iterable
    {
        $this->exists = $attachment->exists;

        return [
            'attachment' => $attachment,
            'config' => $this->config,
            'id' => $attachment->getKey(),
            'alt' => $attachment->getAttribute('alt') ?? $attachment->getAttribute('original_name'),
            'url' => $attachment->url(),
            'isImage' => $attachment->isImage(),
        ];
    }

    public function permission(): ?iterable
    {
        return $this->permissible->accessFileUpdates();
    }

    public function commandBar(): iterable
    {
        return [];
    }

    public function layout(): iterable
    {
        $method = $this->exists ? 'update' : 'upload';

        return [
            LayoutFactory::component(Preview::class)
                ->canSee($this->isImage),
            LayoutFactory::block([
                FileEditLayout::class,
            ])
                ->title('File Information')
                ->description('Basic information about the file')
                ->commands([
                    Button::make()
                        ->type(new Color('default'))
                        ->icon('pencil')
                        ->name('Save')
                        ->method($method),
                ]),
        ];
    }

    public function description(): ?string
    {
        return 'Be careful when modifying data in the file because some data are important components, consult with the developer before making changes.';
    }

    public function name(): ?string
    {
        return 'File';
    }

    public function update(Attachment $attachment, UpdateRequest $request): RedirectResponse
    {
        /** @var array<string, mixed> $data */
        $data = $request->post('attachment');

        $this->updateService->update($attachment->id, $data);

        /** @var string $successMessage */
        $successMessage = $this->translator->get('File Information Updated');

        $this->toast->success($successMessage);

        /** @var string $viewRoute */
        $viewRoute = $this->config->get('orchid-files.routes.view');

        return $this->redirector->route($viewRoute, $attachment->id);
    }

    public function upload(UploadRequest $request): RedirectResponse
    {
        /** @var array<string, mixed> $data */
        $data = $request->post('attachment');

        $file = $request->file('attachment.source');

        assert($file instanceof UploadedFile);

        $attachment = $this->uploadService->upload($file, $data);

        /** @var string $successMessage */
        $successMessage = $this->translator->get('New File successfully added');

        $this->toast->success($successMessage);

        /** @var string $viewRoute */
        $viewRoute = $this->config->get('orchid-files.routes.view');

        return $this->redirector->route($viewRoute, $attachment->id);
    }
}
