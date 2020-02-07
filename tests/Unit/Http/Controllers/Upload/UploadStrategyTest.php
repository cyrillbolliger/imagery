<?php

namespace Tests\Unit;

use App\Http\Controllers\Upload\RegularUploadStrategy;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

class UploadStrategyTest extends TestCase
{
    use WithFaker;

    public function test__construct__createUploadDir()
    {
        $uploadDir = trim(config('app.uploads_dir'), '/');

        Storage::deleteDirectory($uploadDir);

        new RegularUploadStrategy([]);

        $this->assertTrue(Storage::exists($uploadDir));
    }

    public function test__construct__removeOldChunks()
    {
        $uploadDir     = trim(config('app.uploads_dir'), '/');
        $uploadDirPath = disk_path($uploadDir);

        $imgOld = $uploadDirPath.DIRECTORY_SEPARATOR.Str::random().'-1.jpg';
        $imgNew = $uploadDirPath.DIRECTORY_SEPARATOR.Str::random().'-2.jpg';

        $srcImg = database_path('factories'.DIRECTORY_SEPARATOR.'Image.jpg');

        copy($srcImg, $imgOld);
        copy($srcImg, $imgNew);

        $ttl = config('app.uploads_ttl');
        touch($imgOld, time() - $ttl - 1);

        new RegularUploadStrategy([]);

        $this->assertFileNotExists($imgOld);
        $this->assertFileExists($imgNew);
    }
}
