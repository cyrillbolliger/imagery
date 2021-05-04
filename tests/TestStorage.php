<?php


namespace Tests;


use Storage;

trait TestStorage
{
    protected function setUpTestStorage(): void
    {
        $copy = [
            'base_logos',
            'fonts',
            'vector_logo_templates_indesign'
        ];

        $delete = [
            'logo_cache',
            'logo_package_cache'
        ];

        $this->copyToTestStorage($copy);
        $this->deleteOnTestStorage($delete);
    }

    private function copyToTestStorage(array $dirs): void
    {
        foreach ($dirs as $dir) {
            $files = Storage::disk('local')->allFiles($dir);

            foreach ($files as $file) {
                Storage::put(
                    $file,
                    Storage::disk('local')->get($file)
                );
            }
        }
    }

    private function deleteOnTestStorage(array $dirs): void
    {
        Storage::disk('local')->delete($dirs);
    }
}
