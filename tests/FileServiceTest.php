<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Khamsolt\Orchid\Files\Contracts\Updatable;
use Khamsolt\Orchid\Files\Contracts\Uploadable;
use Khamsolt\Orchid\Files\FileGenerator;
use Khamsolt\Orchid\Files\Models\Attachment;
use Khamsolt\Orchid\Files\Tests\TestCase;

uses(RefreshDatabase::class);

it('can available upload service', function () {
    /** @var $this TestCase */
    Storage::fake('local');

    $uploadManager = $this->app->make(Uploadable::class);

    expect($uploadManager)->not()->toBeNull();

    expect($uploadManager)->toBeInstanceOf(Uploadable::class);
});

it('can upload file', function () {
    /** @var $this TestCase */
    Storage::fake('local');

    $file = UploadedFile::fake()->create('file.pdf');

    $uploadManager = $this->app->make(Uploadable::class);

    $attachment = $uploadManager->upload($file, []);

    expect($attachment)->toBeInstanceOf(Attachment::class);

    expect($attachment->exists)->toBeTrue();

    expect($attachment->original_name)->toEqual($file->getClientOriginalName());

    expect($attachment->extension)->toEqual($file->extension());

    expect($attachment->size)->toEqual($file->getSize());

    $path = storage_path('app/public/'.$attachment->physicalPath());

    $this->assertFileExists($path);
});

it('can update attachment information', function () {
    /** @var $this TestCase */
    Storage::fake('local');

    $file = UploadedFile::fake()->create('file.pdf');

    $uploadManager = $this->app->make(Uploadable::class);
    $attachment = $uploadManager->upload($file, []);
    $updaterManager = $this->app->make(Updatable::class);

    $updaterManager->update($attachment->id, [
        'original_name' => 'Test Name.pdf',
        'sort' => 10,
        'description' => 'test description',
        'alt' => 'test alt',
        'group' => 'test group',
    ]);

    $replicate = $attachment->replicate();

    $attachment->refresh();

    $fileGenerator = new FileGenerator($file);

    expect($attachment->original_name)->toEqual('Test Name.pdf');
    expect($attachment->sort)->toEqual(10);
    expect($attachment->description)->toEqual('test description');
    expect($attachment->alt)->toEqual('test alt');
    expect($attachment->group)->toEqual('test group');
    expect($attachment->hash)->toEqual($fileGenerator->hash());
    expect($attachment->name)->toEqual($replicate->name);
    expect($attachment->mime)->toEqual($fileGenerator->mime());
    expect($attachment->extension)->toEqual($fileGenerator->extension());
    expect($attachment->path)->toEqual(Str::finish($fileGenerator->path(), '/'));
});
