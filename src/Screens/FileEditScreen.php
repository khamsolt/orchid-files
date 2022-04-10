<?php

namespace Khamsolt\Orchid\Files\Screens;

use Khamsolt\Orchid\Files\Authorization\Permissions;
use Khamsolt\Orchid\Files\Contracts\Updatable;
use Khamsolt\Orchid\Files\Data\Transfer\AttachmentObject;
use Khamsolt\Orchid\Files\Http\Requests\UpdateRequest;
use Khamsolt\Orchid\Files\Models\Attachment;
use Khamsolt\Orchid\Files\Layouts\FileEditLayout;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Orchid\Alert\Toast;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\LayoutFactory;
use Orchid\Screen\Screen;
use Orchid\Support\Color;

class FileEditScreen extends Screen
{
    private LayoutFactory $layoutFactory;

    private Updatable $updateService;

    private Redirector $redirector;

    private Toast $toast;

    public function __construct(LayoutFactory $layoutFactory, Updatable $updateService, Redirector $redirector, Toast $toast)
    {
        $this->layoutFactory = $layoutFactory;
        $this->updateService = $updateService;
        $this->redirector    = $redirector;
        $this->toast         = $toast;

        $this->permission = Permissions::accessFileUpdates();
    }

    public function query(Attachment $attachment): iterable
    {
        return [
            'attachment' => $attachment
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

    public function commandBar(): iterable
    {
        return [];
    }

    public function layout(): iterable
    {
        return [
            $this->layoutFactory->block([
                FileEditLayout::class
            ])
                ->title('File Information')
                ->description('Basic information about the file')
                ->commands([
                    Button::make()
                        ->type(Color::DEFAULT())
                        ->icon('pencil')
                        ->name('Update')
                        ->method('update')
                ])
        ];
    }

    public function update(Attachment $attachment, UpdateRequest $request): RedirectResponse
    {
        $dto = new AttachmentObject($request->post('attachment'));

        $this->updateService->update($attachment->id, $dto);

        $this->toast->success(__('File Information Updated'));

        return $this->redirector->route('platform.systems.files.show', $attachment->id);
    }
}
