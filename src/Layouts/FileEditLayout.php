<?php

namespace Khamsolt\Orchid\Files\Layouts;


use Orchid\Platform\Models\User;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Relation;
use Orchid\Screen\Layouts\Rows;

class FileEditLayout extends Rows
{
    protected function fields(): iterable
    {
        return [
            Relation::make('attachment.user_id')
                ->required()
                ->title('User')
                ->fromModel(User::class, 'id')
                ->searchColumns('id', 'email', 'username', 'phone_number')
                ->displayAppend('email'),

            Input::make('attachment.original_name')
                ->required()
                ->type('text')
                ->title('Title'),

            Input::make('attachment.sort')
                ->min(0)
                ->type('number')
                ->title('Sort'),

            Input::make('attachment.description')
                ->type('text')
                ->title('Description'),

            Input::make('attachment.alt')
                ->type('text')
                ->title('Alt'),

            Input::make('attachment.group')
                ->type('text')
                ->title('Group'),
        ];
    }
}
