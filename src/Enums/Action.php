<?php

namespace Khamsolt\Orchid\Files\Enums;

enum Action: string
{
    case LIST = 'list';
    case VIEW = 'view';
    case EDIT = 'edit';
    case UPLOAD = 'upload';
    case ATTACH = 'attach';
    case ASSIGN = 'assign';
    case DELETE = 'delete';

    case MAIN = 'main';
    case GROUP = 'group';

    /**
     * @return array<string, string>
     */
    public static function labels(): array
    {
        return [
            self::LIST->value => 'List',
            self::VIEW->value => 'View',
            self::EDIT->value => 'Edit&Update',
            self::DELETE->value => 'Delete',
            self::UPLOAD->value => 'Upload',
            self::ATTACH->value => 'Attach',
            self::ASSIGN->value => 'Assign',
        ];
    }

    public function label(): string
    {
        return match ($this) {
            self::LIST => 'List',
            self::VIEW => 'View',
            self::EDIT => 'Edit&Update',
            self::DELETE => 'Delete',
            self::UPLOAD => 'Upload',
            self::ATTACH => 'Attach',
            self::ASSIGN => 'Assign',
        };
    }

    /**
     * @param  string[]  $keys
     * @return string[]
     */
    public function with(array $keys): array
    {
        return [$this->value, ...$keys];
    }
}
