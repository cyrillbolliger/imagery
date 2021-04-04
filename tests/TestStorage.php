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
        ];

        $this->copyToTestStorage($copy);
    }

    private function copyToTestStorage(array $dirs): void
    {
        foreach ($dirs as $dir) {
            $files = Storage::disk('local')->files($dir);

            foreach ($files as $file) {
                Storage::put(
                    $file,
                    Storage::disk('local')->get($file)
                );
            }
        }
    }
}
