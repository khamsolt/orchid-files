<?php

namespace Khamsolt\Orchid\Files\Layouts;

use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Support\Arr;
use Khamsolt\Orchid\Files\Contracts\Permissions;
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

    public function __construct(private readonly UrlGenerator $generator)
    {
        $this->filePresenter = new FileSettings();
    }

    protected function columns(): iterable
    {
        /** @var array<string|mixed> $config */
        $config = $this->config()->get('orchid-files');

        /** @var array<string, class-string> $presenters */
        $presenters = $config['presenters'] ?? [];

        /** @var string $viewRoute */
        $viewRoute = Arr::get($config, 'routes.view');

        return array_merge($this->selection(), [
            TD::make('id', '#ID')->sort()->defaultHidden()->filter(TD::FILTER_NUMERIC),

            TD::make('original_name', 'Original Name')->sort()->filter(TD::FILTER_TEXT)
                ->render(fn (Attachment $attachment) => new Thumbnail($attachment, $this->generator->route($viewRoute, $attachment->getKey()))),

            TD::make('user_id', 'User')->sort()->filter(Relation::make()->fromModel(User::class, 'id')->displayAppend('list_item'))
                ->render(fn (Attachment $attachment) => $attachment->getRelation('user')
                    ? new Persona($this->filePresenter->resolveUserPresenter($attachment, $presenters)) : null),

            TD::make('name', 'Name')->sort()->defaultHidden()->filter(TD::FILTER_TEXT),

            TD::make('mime', 'Mime')->sort()->defaultHidden()->filter(TD::FILTER_TEXT),

            TD::make('extension', 'Extension')->sort()->defaultHidden()->filter(TD::FILTER_TEXT),

            TD::make('size', 'Size')->sort()->filter(TD::FILTER_NUMERIC)
                ->render(fn (Attachment $attachment) => $attachment->sizeToKb() . ' Kb'),

            TD::make('sort', 'Sort')->sort()->defaultHidden()->filter(TD::FILTER_NUMERIC),

            TD::make('path', 'Path')->sort()->defaultHidden()->filter(TD::FILTER_TEXT),

            TD::make('description', 'Description')->sort()->defaultHidden()->filter(TD::FILTER_TEXT),

            TD::make('alt', 'Alt')->sort()->defaultHidden()->filter(TD::FILTER_TEXT),

            TD::make('hash', 'Hash')->sort()->defaultHidden()->filter(TD::FILTER_TEXT),

            TD::make('disk', 'Disk')->sort()->defaultHidden()->filter(TD::FILTER_TEXT),

            TD::make('group', 'Group')->sort()->defaultHidden()->filter(TD::FILTER_TEXT),

            TD::make('created_at', 'Created')->sort()->filter(TD::FILTER_DATE_RANGE)
                ->render(fn (Attachment $attachment) => $attachment->created_at->toDateTimeString()),

            TD::make('updated_at', 'Updated')->sort()->defaultHidden()->filter(TD::FILTER_DATE_RANGE)
                ->render(fn (Attachment $attachment) => $attachment->created_at->toDateTimeString()),

            TD::make('Actions')->cantHide()->canSee($this->user()
                ->hasAnyAccess(
                    array_merge(
                        (array)$this->permissible()->accessViewFile(),
                        (array)$this->permissible()->accessFileUpdates()
                    )
                ))
                ->align(TD::ALIGN_CENTER)
                ->width('100px')
                ->render(
                    fn (Attachment $attachment) => DropDown::make()
                        ->icon('options-vertical')
                        ->list([
                            Link::make()
                                ->name('View')
                                ->route($this->resolveViewRoute($config), ['attachment' => $attachment->id])
                                ->icon('eye')
                                ->canSee($this->user()->hasAnyAccess($this->permissible()->accessViewFile())),

                            Link::make()
                                ->name('Edit')
                                ->route($this->resolveEditRoute($config), ['attachment' => $attachment->id])
                                ->icon('pencil')
                                ->canSee($this->user()->hasAnyAccess($this->permissible()->accessFileUpdates())),

                            Button::make('Delete')
                                ->icon('trash')
                                ->confirm('Attention, the file you selected will be deleted.')
                                ->method('delete', ['attachment' => $attachment->id]),
                        ])
                ),
        ]);
    }

    /**
     * @param array<string, mixed> $data
     * @return string
     */
    protected function resolveViewRoute(array $data): string
    {
        /** @var string $route */
        $route = Arr::get($data, 'routes.view');

        return $route;
    }

    /**
     * @param array<string, mixed> $data
     * @return string
     */
    protected function resolveEditRoute(array $data): string
    {
        /** @var string $route */
        $route = Arr::get($data, 'routes.edit');

        return $route;
    }

    protected function config(): Repository
    {
        /** @var Repository $result */
        $result = $this->query->get('config');

        return $result;
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

    protected function user(): User
    {
        /** @var User $result */
        $result = $this->query->get('user');

        return $result;
    }

    protected function permissible(): Permissions
    {
        /** @var Permissions $result */
        $result = $this->query->get('permissible');

        return $result;
    }
}
