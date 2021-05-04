<?php

namespace Tests\Feature\Services;

use Database\Seeders\RootSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Logo;
use App\Services\LogoPackageService;
use Tests\TestStorage;

class LogoPackageServiceTest extends TestCase
{
    use RefreshDatabase;
    use TestStorage;

    public function setUp(): void
    {
        parent::setUp();

        $this->seed(RootSeeder::class);
        $this->setUpTestStorage();
    }

    public function testGeneratePackage()
    {
        /** @var Logo $logo */
        $logo = factory(Logo::class)->create();

        $service = new LogoPackageService();
        $path = $service->generatePackage($logo, true);

        self::assertEquals('zip', pathinfo($path)['extension']);
        self::assertFileExists(disk_path($path));


        $expectedFileExtensions = [
            'indd',
            'idml',
            'otf',
            'png',
        ];
        $actualFileExtensions = [];

        $zip = new \ZipArchive;
        if ($zip->open(disk_path($path)) === true) {
            for($i = 0; $i < $zip->numFiles; $i++) {
                $filename = $zip->getNameIndex($i);
                $fileinfo = pathinfo($filename);
                $actualFileExtensions[] = $fileinfo['extension'];
            }
            $zip->close();
        }

        foreach($expectedFileExtensions as $extension) {
            self::assertContains($extension, $actualFileExtensions, ".$extension file missing in package");
        }
    }
}
