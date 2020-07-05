<?php

namespace App;

use App\Http\Controllers\Upload\RegularUploadStrategy;
use App\Http\Controllers\Upload\UploadStrategy;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;


class RegularUploadStrategyTest extends TestCase
{
    use WithFaker;

    public function testStoreTmp()
    {
        $filename = 'some-image.jpeg';

        $file = UploadedFile::fake()->image($filename);

        $request = new \Illuminate\Http\Request([
            'file' => $file
        ]);

        $uploader = new RegularUploadStrategy(['jpeg']);

        $uploader->storeTmp($request);

        $relFilePath = $this->getTmpFilePath($uploader, $filename);
        Storage::assertExists($relFilePath);
    }

    private function getTmpFilePath(UploadStrategy $uploader, string $filename)
    {
        $getRelTmpPath = new \ReflectionMethod($uploader, 'getRelTmpPath');
        $getRelTmpPath->setAccessible(true);

        return $getRelTmpPath->invoke($uploader, $filename);
    }

    public function testStoreTmp__invalidExt()
    {
        $filename = 'some-image.php';

        $file = UploadedFile::fake()->image($filename);

        $request = new \Illuminate\Http\Request([
            'file' => $file
        ]);

        $uploader = new RegularUploadStrategy(['jpeg']);

        $this->expectException(\Illuminate\Validation\ValidationException::class);
        $uploader->storeTmp($request);
    }

    public function testStoreTmp__fileSizeExceeded()
    {
        Config::set('app.uploads_max_file_size', 6 / (1024 * 1024)); // 6 Bytes

        $filename = 'some-image.php';

        $file = UploadedFile::fake()->image($filename);

        $request = new \Illuminate\Http\Request([
            'file' => $file
        ]);

        $uploader = new RegularUploadStrategy(['jpeg']);

        $this->expectException(\Illuminate\Validation\ValidationException::class);
        $uploader->storeTmp($request);
    }
}
