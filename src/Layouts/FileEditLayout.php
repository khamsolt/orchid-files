<?php

namespace Khamsolt\Orchid\Files\Layouts;

use Khamsolt\Orchid\Files\Contracts\Configuration;
use Khamsolt\Orchid\Files\Models\Attachment;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Relation;
use Orchid\Screen\Layouts\Rows;

class FileEditLayout extends Rows
{
    public function __construct(
        private readonly Attachment $attachment,
        private readonly Configuration $configuration
    ) {
    }

    protected function fields(): iterable
    {
        return [
            Input::make('attachment.source')
                ->required(! $this->attachment->exists)
                ->title('File')
                ->type('file')
                ->disabled($this->attachment->exists),

            Relation::make('attachment.user_id')
                ->required()
                ->title('User')
                ->fromModel($this->configuration->user('model'), 'id')
                ->searchColumns(...$this->configuration->userColumns())
                ->displayAppend($this->configuration->user('displayed')),

            Input::make('attachment.original_name')
                ->required($this->attachment->exists)
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
