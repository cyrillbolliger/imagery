<?php

if ( ! function_exists('disk_path')) {
    /**
     * Get the path to the storage folder on the default disk.
     *
     * @param  string  $path
     *
     * @return string
     */
    function disk_path($path = '')
    {
        $disk = config('filesystems.default');
        $base = config("filesystems.disks.$disk.root");

        return $base.($path ? DIRECTORY_SEPARATOR.$path : $path);
    }

    /**
     * Get the path relative to the storage folder.
     *
     * @param  string  $path
     *
     * @return string
     */
    function relative_storage_path($path = '')
    {
        return str_replace(storage_path(), '', $path);
    }
}
