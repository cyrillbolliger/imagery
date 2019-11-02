<?php

namespace App\Observers;

use App\Image;
use Illuminate\Support\Facades\Auth;

class ImageObserver
{
    /**
     * Handle the image "creating" event.
     *
     * @param  \App\Image  $image
     *
     * @return void
     */
    public function creating(Image $image)
    {
        // only fire for authenticated users so we don't kill the seeder
        if (Auth::user()) {
            $image->user()->associate(Auth::user());
        }

        $this->setImageDims($image);
    }

    /**
     * Read the image dimensions from the file and set them on the given image
     *
     * @param  Image  $image
     */
    private function setImageDims(Image $image)
    {
        list($width, $height) = getimagesize(disk_path($image->getRelPath()));
        $image->width  = $width;
        $image->height = $height;
    }

    /**
     * Handle the image "updating" event.
     *
     * @param  \App\Image  $image
     *
     * @return void
     */
    public function updating(Image $image)
    {
        $this->setImageDims($image);
    }

    /**
     * Handle the image "deleted" event.
     *
     * @param  \App\Image  $image
     *
     * @return void
     */
    public function deleted(Image $image)
    {
        //
    }

    /**
     * Handle the image "restored" event.
     *
     * @param  \App\Image  $image
     *
     * @return void
     */
    public function restored(Image $image)
    {
        //
    }

    /**
     * Handle the image "force deleted" event.
     *
     * @param  \App\Image  $image
     *
     * @return void
     */
    public function forceDeleted(Image $image)
    {
        //
    }
}
