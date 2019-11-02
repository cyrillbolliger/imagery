<?php

namespace App\Http\Controllers;

use App\Exceptions\ThumbnailException;
use App\Http\Controllers\Upload\RegularUploadStrategy;
use App\Image;
use App\Rules\EmptyIfRule;
use App\Rules\FileExtensionRule;
use App\Rules\ImageBackgroundRule;
use App\Rules\ImageLogoRule;
use App\Rules\ImageOriginalRule;
use App\Rules\ImmutableRule;
use App\Rules\UserLogoRule;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;

class ImageController extends Controller
{
    private const ALLOWED_EXT = ['png', 'svg', 'jpg', 'jpeg'];

    /**
     * Return paginated list of all shareable raw images.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexRaw()
    {
        return Image::raw()
                    ->shareable()
                    ->orderBy('created_at')
                    ->paginate(50);
    }

    /**
     * Return paginated list of all final images.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexFinal()
    {
        return Image::final()
                    ->orderBy('created_at')
                    ->paginate(50);
    }

    /**
     * Return single raw image.
     *
     * @param  \App\Image  $image
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Image $image)
    {
        $image->user; // load relation

        return $image;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Image  $image
     *
     * @return Response
     * @throws \Exception
     */
    public function destroy(Image $image)
    {
        if ($image->legal) {
            if ( ! $image->legal->delete()) {
                return response('Could not delete images legal information. Image not deleted.', 500);
            }
        }

        if ( ! $image->delete()) {
            return response('Could not delete image.', 500);
        }

        return response(null, 204);
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

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  \App\Image  $image
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Image $image)
    {
        /** @var User $user */
        $user = Auth::user();

        $data = $request->validate([
            'id'          => ['sometimes', new ImmutableRule($image)],
            'user_id'     => ['sometimes', new ImmutableRule($image)],
            'logo_id'     => [
                'sometimes',
                'nullable',
                'exists:logos,id',
                new UserLogoRule($user),
                new ImageLogoRule($image),
            ],
            'background'  => [
                'sometimes',
                'required',
                'in:'.Image::BG_CUSTOM.','.Image::BG_TRANSPARENT.','.Image::BG_GRADIENT,
                new ImageBackgroundRule($image),
            ],
            // if mutable we would have to check the legal etc
            'type'        => ['sometimes', new ImmutableRule($image)],
            'original_id' => [
                'sometimes',
                new ImageOriginalRule($image),
            ],
            'filename'    => [
                'sometimes',
                'required',
                'string',
                'max:255',
                new FileExtensionRule(self::ALLOWED_EXT)
            ],
            'width'       => ['sometimes', new ImmutableRule($image)],
            'height'      => ['sometimes', new ImmutableRule($image)],
            'created_at'  => ['sometimes', new ImmutableRule($image)],
            'updated_at'  => ['sometimes', new ImmutableRule($image)],
            'deleted_at'  => ['sometimes', new ImmutableRule($image)],
        ]);

        if ($request->has('filename')) {
            $handler          = $this->makeUploadHandler($data['filename']);
            $data['filename'] = $handler->storeFinal(Image::getImageStorageDir());
        }

        $image->fill($data);

        $this->generateThumbnail($image);

        if ( ! $image->save()) {
            return response('Could not save image.', 500);
        }

        return $image;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @param  Image  $image
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Image $image)
    {
        $user = Auth::user();

        $data = $request->validate([
            'id'          => ['sometimes', new ImmutableRule($image)],
            'user_id'     => ['sometimes', new ImmutableRule($image)],
            'logo_id'     => [
                'sometimes',
                'nullable',
                'exists:logos,id',
                new UserLogoRule($user),
                new ImageLogoRule($image),
            ],
            'background'  => [
                'required',
                'in:'.Image::BG_CUSTOM.','.Image::BG_TRANSPARENT.','.Image::BG_GRADIENT,
                new ImageBackgroundRule($image),
            ],
            'type'        => ['required', 'in:'.Image::TYPE_RAW.','.Image::TYPE_FINAL],
            'original_id' => [
                new ImageOriginalRule($image),
            ],
            'filename'    => [
                'required',
                'string',
                'max:255',
                new FileExtensionRule(self::ALLOWED_EXT)
            ],
            'width'       => ['sometimes', new ImmutableRule($image)],
            'height'      => ['sometimes', new ImmutableRule($image)],
            'created_at'  => ['sometimes', new ImmutableRule($image)],
            'updated_at'  => ['sometimes', new ImmutableRule($image)],
            'deleted_at'  => ['sometimes', new ImmutableRule($image)],
        ]);

        $handler          = $this->makeUploadHandler($data['filename']);
        $data['filename'] = $handler->storeFinal(Image::getImageStorageDir());

        $image->fill($data);

        $this->generateThumbnail($image);

        if ( ! $image->save()) {
            return response('Could not save image.', 500);
        }

        return $image;
    }

    /**
     * Just a wrapper to handle errors of the generateThumbnail method of the
     * model.
     *
     * @param  Image  $image
     *
     * @return Response|void
     */
    private function generateThumbnail(Image $image)
    {
        try {
            $image->generateThumbnail();
        } catch (\ImagickException | ThumbnailException $e) {
            Log::error('Failed to generate thumbnail. File: '.$image->filename);

            return response('Internal Server Error.', 500);
        }
    }

    /**
     * UploadHandler factory
     *
     * @param  string  $filename
     *
     * @return RegularUploadStrategy
     */
    private function makeUploadHandler(string $filename)
    {
        return new RegularUploadStrategy(
            self::ALLOWED_EXT,
            $filename
        );
    }
}
