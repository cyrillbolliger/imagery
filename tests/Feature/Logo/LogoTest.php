<?php

namespace Tests\Feature;

use App\Logo\Logo;
use App\Logo\LogoFactory;
use Database\Seeders\RootSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use Tests\TestStorage;

class LogoTest extends TestCase
{
    use RefreshDatabase;
    use TestStorage;

    public function setUp(): void
    {
        parent::setUp();

        $this->seed(RootSeeder::class);
        $this->setUpTestStorage();
    }

    public function testGetPng_general()
    {
        $logo = LogoFactory::get('gruene', Logo::LOGO_COLOR_DARK, ['subline']);
        $path = $logo->getPng(200, true);

        self::assertFileExists(disk_path($path));
        self::assertEquals('image/png', Storage::mimeType($path));

        [$width] = getimagesize(disk_path($path));
        self::assertGreaterThanOrEqual(199, $width);
        self::assertLessThanOrEqual(201, $width);
    }

    public function testGetPng_typesAndSchemes()
    {
        $types = [
            'alternative',
            'gruene',
            'gruene-vert-e-s',
            'gruene-verts',
            'verda',
            'verdi',
            'vert-e-s',
            'verts'
        ];
        $schemes = [
            Logo::LOGO_COLOR_DARK,
            Logo::LOGO_COLOR_LIGHT
        ];
        foreach($types as $type) {
            foreach ($schemes as $scheme) {
                $logo = LogoFactory::get($type, $scheme, ['subline']);
                $path = $logo->getPng(200, true);

                self::assertFileExists(disk_path($path));
            }
        }
    }
}
