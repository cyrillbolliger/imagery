<?php

namespace App\Http\Controllers;

use App\Http\Resources\ImageResource;
use App\Image;
use Illuminate\Http\Request;

class ImageController extends Controller
{
    /**
     * Return paginated list of all raw images.
     *
     * @return \Illuminate\Http\Response
     */
    public function listRaw()
    {
        return ImageResource::collection(
            Image::whereNull('original_id')->paginate(10)
        );
    }

    /**
     * Store a raw image. Update if it contains an id, else insert.
     *
     * @param  \Illuminate\Http\Request  $request
     *
     * @return \Illuminate\Http\Response
     */
    public function storeRaw(Request $request)
    {
        //
    }

    /**
     * Return single raw image.
     *
     * @param  \App\Image  $image
     *
     * @return \Illuminate\Http\Response
     */
    public function getRaw(Image $image)
    {
        //
    }

    /**
     * Soft delete raw image.
     *
     * @param  \App\Image  $image
     *
     * @return \Illuminate\Http\Response
     */
    public function deleteRaw(Image $image)
    {
        //
    }

    /**
     * Return paginated list of raw images matching the query.
     *
     * @param  \App\Image  $image
     *
     * @return \Illuminate\Http\Response
     */
    public function searchRaw(Image $image)
    {
        //
    }
}
