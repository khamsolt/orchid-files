<?php

namespace Khamsolt\Orchid\Files\Layouts;

use App\Models\User;
use Illuminate\Contracts\Config\Repository;
use Khamsolt\Orchid\Files\Authorization\Permissions;
use Khamsolt\Orchid\Files\Contracts\Entities\Permissible;
use Khamsolt\Orchid\Files\Models\Attachment;
use Khamsolt\Orchid\Files\View\Components\Thumbnail;
use Orchid\Screen\Actions\DropDown;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Fields\CheckBox;
use Orchid\Screen\Fields\Radio;
use Orchid\Screen\Fields\Relation;
use Orchid\Screen\Layouts\Persona;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;

class FileListLayout extends Table
{
    protected $target = 'files';

    protected function selection(): array
    {
        $mode = $this->query->get('mode');

        $selection = [];

        if ($mode === 'checkbox') {
            $selection[] = $this->checkbox();
        } elseif ($mode === 'radio') {
            $selection[] = $this->radio();
        }

        return $selection;
    }

    protected function columns(): iterable
    {
        return array_merge($this->selection(), [
            TD::make('id', '#ID')
                ->sort()
                ->defaultHidden()
                ->filter(TD::FILTER_NUMERIC),

            TD::make('original_name', 'Original Name')
                ->sort()
                ->filter(TD::FILTER_TEXT)
                ->render(fn (Attachment $attachment) => new Thumbnail($attachment->getAttribute('original_name'), $attachment->thumbnail())),

            TD::make('user_id', 'User')
                ->sort()
                ->filter(Relation::make()
                        ->fromModel(User::class, 'id')
                        ->displayAppend('list_item'))
                ->render(fn (Attachment $attachment) => new Persona($attachment->user->presenter())),

            TD::make('name', 'Name')
                ->sort()
                ->defaultHidden()
                ->filter(TD::FILTER_TEXT),

            TD::make('mime', 'Mime')
                ->sort()
                ->defaultHidden()
                ->filter(TD::FILTER_TEXT),

            TD::make('extension', 'Extension')
                ->sort()
                ->defaultHidden()
                ->filter(TD::FILTER_TEXT),

            TD::make('size', 'Size')
                ->sort()
                ->filter(TD::FILTER_NUMERIC)
                ->render(fn (Attachment $attachment) => $attachment->sizeToKb() . ' Kb'),

            TD::make('sort', 'Sort')
                ->sort()
                ->defaultHidden()
                ->filter(TD::FILTER_NUMERIC),

            TD::make('path', 'Path')
                ->sort()
                ->defaultHidden()
                ->filter(TD::FILTER_TEXT),

            TD::make('description', 'Description')
                ->sort()
                ->defaultHidden()
                ->filter(TD::FILTER_TEXT),

            TD::make('alt', 'Alt')
                ->sort()
                ->defaultHidden()
                ->filter(TD::FILTER_TEXT),

            TD::make('hash', 'Hash')
                ->sort()
                ->defaultHidden()
                ->filter(TD::FILTER_TEXT),

            TD::make('disk', 'Disk')
                ->sort()
                ->defaultHidden()
                ->filter(TD::FILTER_TEXT),

            TD::make('group', 'Group')
                ->sort()
                ->defaultHidden()
                ->filter(TD::FILTER_TEXT),

            TD::make('created_at', 'Created')
                ->sort()
                ->filter(TD::FILTER_DATE_RANGE)
                ->render(fn (Attachment $attachment) => $attachment->created_at?->toDateTimeString()),

            TD::make('updated_at', 'Updated')
                ->sort()
                ->defaultHidden()
                ->filter(TD::FILTER_DATE_RANGE)
                ->render(fn (Attachment $attachment) => $attachment->created_at?->toDateTimeString()),

            TD::make(__('Actions'))
                ->cantHide()
                ->canSee($this->query->get('user')
                    ->hasAnyAccess($this->permissible()->accessViewFile() + $this->permissible()->accessFileUpdates()))
                ->align(TD::ALIGN_CENTER)
                ->width('100px')
                ->render(
                    fn (Attachment $attachment) => DropDown::make()
                        ->icon('options-vertical')
                        ->list([
                            Link::make()
                                ->name('View')
                                ->route($this->config()->get('orchid-files.routes.view'), $attachment->getKey())
                                ->icon('eye')
                                ->canSee($this->user()->hasAnyAccess($this->permissible()->accessViewFile())),

                            Link::make()
                                ->name('Edit')
                                ->route($this->config()->get('orchid-files.routes.edit'), $attachment->getKey())
                                ->icon('pencil')
                                ->canSee($this->user()->hasAnyAccess($this->permissible()->accessFileUpdates())),
                        ])
                ),
        ]);
    }

    protected function radio(): TD
    {
        return TD::make()->render(fn (Attachment $attachment) => Radio::make('files[]')
            ->value($attachment->id)
            ->checked(false));
    }

    protected function checkbox(): TD
    {
        return TD::make()->render(fn(Attachment $attachment) => CheckBox::make('files[]')
            ->value($attachment->id)
            ->checked(false));
    }

    protected function permissible(): Permissible
    {
        return $this->query->get('permissible');
    }

    protected function config(): Repository
    {
        return $this->query->get('config');
    }

    protected function user(): User
    {
        return $this->query->get('user');
    }
}
