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
        $this->extendKeywords($image);
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
     * Append the logo name and the username to the image
     *
     * @param  Image  $image
     */
    private function extendKeywords(Image $image)
    {
        if (Image::TYPE_FINAL === $image->type && $image->logo_id) {
            $image->keywords .= ' '.$image->logo->name;
        }

        $image->keywords .= ' '.$image->user->first_name.' '.$image->user->last_name;
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
}
