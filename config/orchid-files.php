<?php return [

    /**
     *  The list route names so than used package
     */
    'route_names' => [
        'files' => 'platform.systems.files',
        'main'  => 'platform.main'
    ],

    /**
     *
     */
    'permissions' => [
        'group'  => 'File Manager',
        'list'   => 'Accessing the file list',
        'show'   => 'Access to view file',
        'assign' => 'Accessing a file assignment',
        'attach' => 'Access to file attachments',
        'update' => 'Access to file updates',
        'upload' => 'Access to file uploads',
    ],

    /**
     *
     */
    'assign_storage' => \Khamsolt\Orchid\Files\Data\Storage\SessionStorage::class,

    /**
     *
     */
    'assign' => \Khamsolt\Orchid\Files\FileAssigment::class,

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
