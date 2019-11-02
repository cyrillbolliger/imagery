<?php

if ( ! function_exists('disk_path')) {
    /**
     * Get the path to the storage folder on the default disk.
     *
     * @param  string  $path
     *
     * @return string
     */
    function disk_path(string $path = ''): string
    {
        $disk = config('filesystems.default');
        $base = config("filesystems.disks.$disk.root");

        return $base.($path ? DIRECTORY_SEPARATOR.$path : $path);
    }

    /**
     * Asserts that the given path exists and creates it if not
     *
     * @param  string  $path
     * @param  string  $visibility
     *
     * @return string
     */
    function create_dir(string $path, $visibility = 'private'): string
    {
        if ( ! \Illuminate\Support\Facades\Storage::exists($path)) {
            \Illuminate\Support\Facades\Storage::makeDirectory($path);
            \Illuminate\Support\Facades\Storage::setVisibility($path, $visibility);
        }

        return $path;
    }
}
