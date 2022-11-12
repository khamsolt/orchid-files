<?php return [
    'table'           => 'attachments',

    'relation_table'  => 'attachmentable',

    'size' => 25,

    'user' => [
        'model' => \App\Models\User::class,

        'columns' => [
            'id',
            'email',
            'name',
            'surname',
            'nickname',
        ],

        'displayed' => 'email'
    ],

    'storage' => [
        'folder' => 'files',
        'chars' => 3,
        'steps' => 3
    ],

    'routes' => [
        'list' => 'platform.files.list',
        'view' => 'platform.files.edit',
        'edit' => 'platform.files.view',
        'upload' => 'platform.files.upload',

        'main' => 'platform.main'
    ],

    'permissions' => [
        'titles' => [
            'group'  => 'File Manager',
            'list'   => 'Accessing the file list',
            'view'   => 'Access to view file',
            'assign' => 'Accessing a file assignment',
            'attach' => 'Access to file attachments',
            'update' => 'Access to file updates',
            'upload' => 'Access to file uploads',
        ],

        'keys' => [
            'list'   => ['platform.files.list'],
            'view'   => ['platform.files.view'],
            'assign' => ['platform.files.assign'],
            'attach' => ['platform.files.attach'],
            'update' => ['platform.files.update'],
            'upload' => ['platform.files.upload'],
        ]
    ],

    'presenters' => [
        'user' => \App\Orchid\Presenters\UserPresenter::class
    ],

    'bind' => [
        'attach' => \Khamsolt\Orchid\Files\FileService::class,

        'update' => \Khamsolt\Orchid\Files\FileService::class,

        'upload' => \Khamsolt\Orchid\Files\FileService::class,

        'search' => \Khamsolt\Orchid\Files\FileRepository::class,

        'attachmentable' => \Khamsolt\Orchid\Files\FileAttachment::class,
    ],

];
