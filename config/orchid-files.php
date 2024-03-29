<?php

use Khamsolt\Orchid\Files\Enums\Action;

return [
    'name' => 'Files',

    'table' => 'attachments',

    'relation_table' => 'attachmentable',

    'size' => 25600,

    'user' => [
        'model' => \App\Models\User::class,

        'columns' => [
            'id',
            'email',
            'name',
        ],

        'displayed' => 'email',

        'presenter' => \App\Orchid\Presenters\UserPresenter::class,
    ],

    'storage' => [
        'folder' => 'files',
        'chars' => 3,
        'steps' => 3,
    ],

    'routes' => [
        Action::LIST->value => 'platform.files.list',
        Action::VIEW->value => 'platform.files.view',
        Action::EDIT->value => 'platform.files.edit',
        Action::UPLOAD->value => 'platform.files.upload',

        Action::MAIN->value => 'platform.main',
    ],

    'permissions' => [
        'titles' => [
            /** @deprecated */
            Action::GROUP->value => 'File Manager',

            Action::LIST->value => 'List',
            Action::VIEW->value => 'View',
            Action::EDIT->value => 'Edit&Update',
            Action::ASSIGN->value => 'Assign',
            Action::ATTACH->value => 'Attach',
            Action::UPLOAD->value => 'Upload',
            Action::DELETE->value => 'Delete',
        ],

        'keys' => [
            Action::LIST->value => 'platform.files.list',
            Action::VIEW->value => 'platform.files.view',
            Action::EDIT->value => 'platform.files.edit',
            Action::ASSIGN->value => 'platform.files.assign',
            Action::ATTACH->value => 'platform.files.attach',
            Action::UPLOAD->value => 'platform.files.upload',
            Action::DELETE->value => 'platform.files.delete',
        ],

        'accesses' => [
            Action::LIST->value => [],
            Action::VIEW->value => [],
            Action::EDIT->value => [],
            Action::ASSIGN->value => [],
            Action::ATTACH->value => [],
            Action::UPLOAD->value => [],
            Action::DELETE->value => [],
        ],
    ],

    /**
     * @deprecated
     */
    'presenters' => [
        'user' => \App\Orchid\Presenters\UserPresenter::class,
    ],

    'bind' => [
        'configuration' => \Khamsolt\Orchid\Files\FileConfigurator::class,

        'authorization' => \Khamsolt\Orchid\Files\FileAuthorize::class,

        'translation' => \Khamsolt\Orchid\Files\FileTranslator::class,

        'attach' => \Khamsolt\Orchid\Files\FileService::class,

        'update' => \Khamsolt\Orchid\Files\FileService::class,

        'upload' => \Khamsolt\Orchid\Files\FileService::class,

        'search' => \Khamsolt\Orchid\Files\FileRepository::class,

        'attachmentable' => \Khamsolt\Orchid\Files\FileAttachment::class,
    ],

    'datetime' => [
        'format' => 'd M Y, H:i:s',
        'timezone' => null,
    ],

];
