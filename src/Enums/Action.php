<?php

namespace Khamsolt\Orchid\Files\Enums;

enum Action: string
{
    case LIST = 'list';
    case EDIT = 'edit';
    case VIEW = 'view';
    case CREATE = 'create';
    case UPLOAD = 'upload';
    case UPDATE = 'update';
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
            self::EDIT->value => 'Edit',
            self::VIEW->value => 'View',
            self::CREATE->value => 'Create',
            self::DELETE->value => 'Delete',
            self::UPLOAD->value => 'Upload',
            self::UPDATE->value => 'Update',
            self::ATTACH->value => 'Attach',
            self::ASSIGN->value => 'Assign'
        ];
    }

    /**
     * @return string
     */
    public function label(): string
    {
        return match ($this) {
            self::LIST => 'List',
            self::EDIT => 'Edit',
            self::VIEW => 'View',
            self::CREATE => 'Create',
            self::DELETE => 'Delete',
            self::UPLOAD => 'Upload',
            self::UPDATE => 'Update',
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
