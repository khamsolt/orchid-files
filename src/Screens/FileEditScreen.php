<?php

namespace Khamsolt\Orchid\Files\Screens;

use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Translation\Translator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Khamsolt\Orchid\Files\Contracts\Entities\Permissible;
use Khamsolt\Orchid\Files\Contracts\Updatable;
use Khamsolt\Orchid\Files\Data\Transfer\AttachmentObject;
use Khamsolt\Orchid\Files\Http\Requests\UpdateRequest;
use Khamsolt\Orchid\Files\Layouts\FileEditLayout;
use Khamsolt\Orchid\Files\Models\Attachment;
use Orchid\Alert\Toast;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\LayoutFactory;
use Orchid\Screen\Screen;
use Orchid\Support\Color;

class FileEditScreen extends Screen
{
    public function __construct(private readonly LayoutFactory $layoutFactory,
                                private readonly Permissible $permissible,
                                private readonly Repository $config,
                                private readonly Updatable $updateService,
                                private readonly Redirector $redirector,
                                private readonly Translator $translator,
                                private readonly Toast $toast)
    {
    }

    public function query(Attachment $attachment): iterable
    {
        return [
            'attachment' => $attachment,
        ];
    }

    public function name(): ?string
    {
        return 'File';
    }

    public function description(): ?string
    {
        return 'Be careful when modifying data in the file because some data are important components, consult with the developer before making changes.';
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
        return [
            $this->layoutFactory->block([
                FileEditLayout::class,
            ])
                ->title('File Information')
                ->description('Basic information about the file')
                ->commands([
                    Button::make()
                        ->type(new Color('default'))
                        ->icon('pencil')
                        ->name('Update')
                        ->method('update'),
                ]),
        ];
    }

    public function update(Attachment $attachment, UpdateRequest $request): RedirectResponse
    {
        $dto = new AttachmentObject($request->post('attachment'));

        $this->updateService->update($attachment->getKey(), $dto);

        $this->toast->success($this->translator->get('File Information Updated'));

        return $this->redirector->route($this->config->get('orchid-files.routes.view'), $attachment->getKey());
    }
}
