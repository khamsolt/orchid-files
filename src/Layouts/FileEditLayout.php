<?php

namespace Khamsolt\Orchid\Files\Layouts;


use Illuminate\Contracts\Config\Repository;
use Illuminate\Support\Arr;
use Khamsolt\Orchid\Files\Models\Attachment;
use Orchid\Platform\Models\User;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Relation;
use Orchid\Screen\Layouts\Rows;

class FileEditLayout extends Rows
{
    /**
     * @param array<string, mixed> $data
     * @return string
     */
    public function resolveUserModel(array $data): string
    {
        /** @var class-string $model */
        $model = $data['model'] ?? User::class;

        return $model;
    }

    /**
     * @param array<string, mixed> $data
     * @return string[]
     */
    public function resolveUserColumns(array $data): array
    {
        /** @var string|string[] $value */
        $value = $data['columns'] ?? ['id', 'email', 'nickname'];

        /** @var string[] $value */
        $value = Arr::wrap($value);

        return $value;
    }

    /**
     * @param array<string, mixed> $data
     * @return string
     */
    public function resolveUserDisplayed(array $data): string
    {
        /** @var string $key */
        $key = $data['displayed'] ?? 'email';

        return $key;
    }

    protected function fields(): iterable
    {
        $attachment = $this->query->get('attachment');

        assert($attachment instanceof Attachment);

        $config = $this->query->get('config');

        assert($config instanceof Repository);

        /** @var array<string, mixed> $userSettings */
        $userSettings = $config->get('orchid-files.user');

        return [
            Input::make('attachment.source')
                ->required(!$attachment->exists)
                ->title('File')
                ->type('file')
                ->disabled($attachment->exists),

            Relation::make('attachment.user_id')
                ->required()
                ->title('User')
                ->fromModel($this->resolveUserModel($userSettings), 'id')
                ->searchColumns(...$this->resolveUserColumns($userSettings))
                ->displayAppend($this->resolveUserDisplayed($userSettings)),

            Input::make('attachment.original_name')
                ->required($attachment->exists)
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
