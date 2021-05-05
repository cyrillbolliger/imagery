<?php

namespace Tests\Unit\Http\Controllers\Upload;

use App\Http\Controllers\Upload\ChunkUploadStrategy;
use App\Http\Controllers\Upload\UploadStrategy;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;


class ChunkUploadStrategyTest extends TestCase
{
    use WithFaker;

    public function testStoreTmp()
    {
        $filename = 'test.txt';

        $chunk1 = 'data:application/octet-stream;base64,MTIzNA'; // 1234
        $chunk2 = 'YXNkZg=='; // asdf

        $request1 = new \Illuminate\Http\Request([
            'filename'   => $filename,
            'part'       => 0,
            'base64data' => $chunk1,
        ]);
        $request2 = new \Illuminate\Http\Request([
            'filename'   => $filename,
            'part'       => 4,
            'base64data' => $chunk2,
        ]);

        $uploader = new ChunkUploadStrategy(['txt']);

        $uploader->storeTmp($request1);
        $uploader->storeTmp($request2);

        $relFilePath = $this->getTmpFilePath($uploader, $filename);
        $content     = Storage::get($relFilePath);

        $this->assertEquals('MTIzNAYXNkZg==', $content);
    }

    private function getTmpFilePath(UploadStrategy $uploader, string $filename)
    {
        $getRelTmpPath = new \ReflectionMethod($uploader, 'getRelTmpPath');
        $getRelTmpPath->setAccessible(true);

        return $getRelTmpPath->invoke($uploader, $filename);
    }

    public function testStoreTmp__invalidExt()
    {
        $filename = 'test.php';

        $chunk = 'data:application/octet-stream;base64,MTIzNA=='; // 1234

        $request = new \Illuminate\Http\Request([
            'filename'   => $filename,
            'part'       => 0,
            'base64data' => $chunk,
        ]);

        $uploader = new ChunkUploadStrategy(['txt']);

        $this->expectException(\Illuminate\Validation\ValidationException::class);
        $uploader->storeTmp($request);
    }

    public function testStoreTmp__fileSizeExceeded()
    {
        Config::set('app.uploads_max_file_size', 6 / (1024 * 1024)); // 6 Bytes

        $filename = 'test.txt';

        $chunk1 = 'data:application/octet-stream;base64,MTIzNA=='; // 1234
        $chunk2 = 'data:application/octet-stream;base64,YXNkZg=='; // asdf

        $request1 = new \Illuminate\Http\Request([
            'filename'   => $filename,
            'part'       => 0,
            'base64data' => $chunk1,
        ]);
        $request2 = new \Illuminate\Http\Request([
            'filename'   => $filename,
            'part'       => 4,
            'base64data' => $chunk2,
        ]);

        $uploader = new ChunkUploadStrategy(['txt']);

        $relFilePath = $this->getTmpFilePath($uploader, $filename);
        Storage::delete($relFilePath);

        $uploader->storeTmp($request1);

        $this->expectException(\Illuminate\Http\Exceptions\HttpResponseException::class);
        $uploader->storeTmp($request2);
    }

    public function testStoreTmp__chunkSizeExceeded()
    {
        Config::set('app.uploads_max_chunk_size', 3 / (1024 * 1024)); // 3 Bytes

        $filename = 'test.txt';

        $chunk = 'data:application/octet-stream;base64,MTIzNA=='; // 1234

        $request = new \Illuminate\Http\Request([
            'filename'   => $filename,
            'part'       => 0,
            'base64data' => $chunk,
        ]);

        $uploader = new ChunkUploadStrategy(['txt']);

        $this->expectException(\Illuminate\Http\Exceptions\HttpResponseException::class);
        $uploader->storeTmp($request);
    }
}
