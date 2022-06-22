<?php return [

    'table'           => 'attachments',

    'relation_table'  => 'attachmentable',

    /**
     *  The list route names so than used package
     */
    'routes' => [
        'list' => 'platform.systems.files.list',
        'view' => 'platform.systems.files.edit',
        'edit' => 'platform.systems.files.view',

        'main' => 'platform.main'
    ],

    /**
     *
     */
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

    /**
     *
     */
    'attach' => \Khamsolt\Orchid\Files\FileService::class,

    /**
     *
     */
    'update' => \Khamsolt\Orchid\Files\FileService::class,

    /**
     *
     */
    'search' => \Khamsolt\Orchid\Files\SearchService::class,

    /**
     *
     */
    'entities' => [
        'attachmentable' => \Khamsolt\Orchid\Files\Entities\Attachmentable::class,

        'permissions'    => \Khamsolt\Orchid\Files\Authorization\Permissions::class,
    ]
];
