<?php

namespace Khamsolt\Orchid\Files\Layouts;

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Routing\UrlGenerator;
use Khamsolt\Orchid\Files\Contracts\Authorization;
use Khamsolt\Orchid\Files\Contracts\Configuration;
use Khamsolt\Orchid\Files\Enums\Action;
use Khamsolt\Orchid\Files\Enums\Mode;
use Khamsolt\Orchid\Files\FileSettings;
use Khamsolt\Orchid\Files\Models\Attachment;
use Khamsolt\Orchid\Files\View\Components\Thumbnail;
use Orchid\Platform\Models\User;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\DropDown;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Cell;
use Orchid\Screen\Fields\CheckBox;
use Orchid\Screen\Fields\Radio;
use Orchid\Screen\Fields\Relation;
use Orchid\Screen\Layouts\Persona;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;

class FileListLayout extends Table
{
    protected $target = 'files';

    protected readonly FileSettings $filePresenter;

    public function __construct(
        private readonly User $user,
        private readonly UrlGenerator $generator,
        private readonly Authorization $authorization,
        private readonly Configuration $configuration
    ) {
        $this->filePresenter = new FileSettings($this->configuration);
    }

    /**
     * @throws BindingResolutionException
     */
    protected function columns(): iterable
    {
        return array_merge($this->selection(), [
            TD::make('id', '#ID')
                ->sort()
                ->defaultHidden()
                ->filter(TD::FILTER_NUMERIC),

            TD::make('original_name', 'Original Name')
                ->sort()
                ->filter()
                ->render(fn (Attachment $attachment) => new Thumbnail(
                    $attachment,
                    $this->generator->route($this->configuration->route(Action::VIEW), $attachment->getKey())
                )),

            TD::make('user_id', 'User')
                ->sort()
                ->filter(
                    Relation::make()
                        ->fromModel(User::class, 'id')
                        ->displayAppend('list_item')
                )
                ->render(
                    fn (Attachment $attachment) => $attachment->getRelation('user')
                        ? new Persona($this->filePresenter->resolveUserPresenter($attachment))
                        : null
                ),

            TD::make('name', 'Name')
                ->sort()
                ->defaultHidden()
                ->filter(),

            TD::make('mime', 'Mime')
                ->sort()
                ->defaultHidden()
                ->filter(),

            TD::make('extension', 'Extension')
                ->sort()
                ->defaultHidden()
                ->filter(),

            TD::make('size', 'Size')
                ->sort()
                ->filter(TD::FILTER_NUMERIC)
                ->render(fn (Attachment $attachment) => $attachment->sizeToKb().' Kb'),

            TD::make('sort', 'Sort')
                ->sort()
                ->defaultHidden()
                ->filter(TD::FILTER_NUMERIC),

            TD::make('path', 'Path')
                ->sort()
                ->defaultHidden()
                ->filter(),

            TD::make('description', 'Description')
                ->sort()
                ->defaultHidden()
                ->filter(),

            TD::make('alt', 'Alt')
                ->sort()
                ->defaultHidden()
                ->filter(),

            TD::make('hash', 'Hash')
                ->sort()
                ->defaultHidden()
                ->filter(),

            TD::make('disk', 'Disk')
                ->sort()
                ->defaultHidden()
                ->filter(),

            TD::make('group', 'Group')
                ->sort()
                ->defaultHidden()
                ->filter(),

            TD::make('created_at', 'Created')
                ->sort()
                ->filter(TD::FILTER_DATE_RANGE)
                ->render(fn (Attachment $attachment) => $attachment->created_at->toDateTimeString()),

            TD::make('updated_at', 'Updated')
                ->sort()
                ->defaultHidden()
                ->filter(TD::FILTER_DATE_RANGE)
                ->render(fn (Attachment $attachment) => $attachment->created_at->toDateTimeString()),

            TD::make('Actions')
                ->cantHide()
                ->align(TD::ALIGN_CENTER)
                ->width('100px')
                ->render(
                    fn (Attachment $attachment) => DropDown::make()
                    ->icon('bs.list')
                    ->list([
                        Link::make()
                            ->name('View')
                            ->route($this->configuration->route(Action::VIEW), ['attachment' => $attachment->id])
                            ->icon('bs.eye')
                            ->canSee($this->user->hasAnyAccess($this->authorization->authorize(Action::VIEW))),

                        Link::make()
                            ->name('Edit')
                            ->route($this->configuration->route(Action::EDIT), ['attachment' => $attachment->id])
                            ->icon('bs.pencil')
                            ->canSee($this->user->hasAnyAccess($this->authorization->authorize(Action::EDIT))),

                        Button::make('Delete')
                            ->icon('bs.trash')
                            ->canSee($this->user->hasAnyAccess($this->authorization->authorize(Action::DELETE)))
                            ->confirm('Attention, the file you selected will be deleted.')
                            ->method('delete', ['attachment' => $attachment->id]),
                    ])
                ),
        ]);
    }

    protected function selection(): array
    {
        /** @var string|null $mode */
        $mode = $this->query->get('mode');

        if ($mode === null) {
            return [];
        }

        $mode = Mode::from($mode);

        $selection = [];

        if ($mode === Mode::MULTIPLE) {
            $selection[] = $this->checkbox();
        } elseif ($mode === Mode::SINGLE) {
            $selection[] = $this->radio();
        }

        return $selection;
    }

    protected function checkbox(): Cell
    {
        return TD::make()
            ->render(fn (Attachment $attachment): CheckBox => CheckBox::make('attachments[]')
                ->value($attachment->getKey())
                ->checked(false));
    }

    protected function radio(): Cell
    {
        return TD::make()
            ->render(fn (Attachment $attachment): Radio => Radio::make('attachments')
                ->set('data-url', $attachment->url())
                ->value($attachment->getKey())
                ->checked(false));
    }
}
