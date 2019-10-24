<?php

namespace Tests\Unit;

use App\Domain\UploadHandler;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class UploadHandlerTest extends TestCase
{
    use WithFaker;

    public function test__construct__createUploadDir()
    {
        $uploadDir = trim(config('app.uploads_dir'), '/');

        Storage::deleteDirectory($uploadDir);

        new UploadHandler();

        $this->assertTrue(Storage::exists($uploadDir));
    }

    public function test__construct__removeOldChunks()
    {
        $uploadDir     = trim(config('app.uploads_dir'), '/');
        $uploadDirPath = disk_path($uploadDir);

        $imgOld = $this->faker->image($uploadDirPath, 50, 50, 'cats', true);
        $imgNew = $this->faker->image($uploadDirPath, 50, 50, 'cats', true);

        $ttl = config('app.uploads_ttl');
        touch($imgOld, time() - $ttl - 1);

        new UploadHandler();

        $this->assertFileNotExists($imgOld);
        $this->assertFileExists($imgNew);
    }

    public function testSaveChunk()
    {
        $fileName = 'test';

        $chunk1 = 'data:application/octet-stream;base64,MTIzNA=='; // 1234
        $chunk2 = 'data:application/octet-stream;base64,YXNkZg=='; // asdf

        $uploader = new UploadHandler();

        $uploader->saveChunk($chunk1, $fileName, 0);
        $uploader->saveChunk($chunk2, $fileName, 5);

        $relFilePath = UploadHandler::getRelDirPath().DIRECTORY_SEPARATOR.$fileName;

        $content = Storage::get($relFilePath);

        $this->assertEquals('1234asdf', $content);
    }
}
