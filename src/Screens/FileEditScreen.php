<?php

namespace Khamsolt\Orchid\Files\Screens;

use Illuminate\Contracts\Config\Repository;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Routing\Redirector;
use Khamsolt\Orchid\Files\Contracts\Authorization;
use Khamsolt\Orchid\Files\Contracts\Configuration;
use Khamsolt\Orchid\Files\Contracts\Translation;
use Khamsolt\Orchid\Files\Contracts\Updatable;
use Khamsolt\Orchid\Files\Contracts\Uploadable;
use Khamsolt\Orchid\Files\Enums\Action;
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

final class FileEditScreen extends Screen
{
    public ?Attachment $attachment = null;

    public ?bool $exists = true;
    public ?bool $isImage = false;

    public function __construct(
        private readonly Authorization $permissible,
        private readonly Repository $config,
        private readonly Configuration $configuration,
        private readonly Updatable $updateService,
        private readonly Uploadable $uploadService,
        private readonly Redirector $redirector,
        private readonly Translation $translator,
        private readonly Toast $toast
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
        return $this->permissible->authorize(Action::EDIT);
    }

    public function commandBar(): iterable
    {
        $method = $this->exists ? 'update' : 'upload';

        return [
            Button::make()
                ->icon('bs.file-arrow-up-fill')
                ->name(ucfirst($method))
                ->method($method),
        ];
    }

    public function name(): ?string
    {
        return 'File';
    }

    public function layout(): iterable
    {
        $method = $this->exists ? 'update' : 'upload';

        return [
            LayoutFactory::component(Preview::class)
                ->canSee($this->isImage),
            LayoutFactory::block([
                new FileEditLayout($this->attachment, $this->configuration),
            ])
                ->title('File Information')
                ->description('Basic information about the file')
                ->commands([
                    Button::make()
                        ->type(Color::DEFAULT)
                        ->icon('bs.file-arrow-up')
                        ->name(ucfirst($method))
                        ->method($method),
                ]),
        ];
    }

    public function description(): ?string
    {
        return 'Be careful when modifying data in the file because some data are important components, consult with the developer before making changes.';
    }

    public function update(Attachment $attachment, UpdateRequest $request): RedirectResponse
    {
        /** @var array<string, mixed> $data */
        $data = $request->post('attachment');

        $this->updateService->update($attachment->id, $data);

        $this->toast->success($this->translator->get('File Information Updated'));

        return $this->redirector->route($this->configuration->route(Action::VIEW), $attachment->id);
    }

    public function upload(UploadRequest $request): RedirectResponse
    {
        /** @var array<string, mixed> $data */
        $data = $request->post('attachment');

        $file = $request->file('attachment.source');

        assert($file instanceof UploadedFile);

        $attachment = $this->uploadService->upload($file, $data);

        $this->toast->success($this->translator->get('New File successfully added'));

        return $this->redirector->route($this->configuration->route(Action::VIEW), $attachment->id);
    }
}
