<?php

namespace Tests\Unit;

use App\Http\Controllers\Upload\RegularUploadStrategy;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Storage;
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

        $imgOld = $this->faker->image($uploadDirPath, 50, 50, 'cats', true);
        $imgNew = $this->faker->image($uploadDirPath, 50, 50, 'cats', true);

        $ttl = config('app.uploads_ttl');
        touch($imgOld, time() - $ttl - 1);

        new RegularUploadStrategy([]);

        $this->assertFileNotExists($imgOld);
        $this->assertFileExists($imgNew);
    }
}
