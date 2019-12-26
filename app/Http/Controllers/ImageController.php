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
                    ->latest()
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
                    ->latest()
                    ->paginate(50);
    }

    /**
     * Return paginated list of matching final images.
     *
     * Search for final images with the given string using MySQL's full text
     * search. All text in the images keyword field is searched. The results
     * are primarily ordered by the best match, secondarily by creation date.
     *
     * @param  string  $terms
     *
     * @return \Illuminate\Http\Response
     */
    public function searchFinal(string $terms)
    {
        $terms = $this->prepareTerms(urldecode($terms));

        if ( ! $terms ) {
            return $this->indexFinal();
        }

        return Image::final()
                    ->selectRaw('*, MATCH (keywords) AGAINST (? IN BOOLEAN MODE) as score', [$terms])
                    ->whereRaw('MATCH (keywords) AGAINST (? IN BOOLEAN MODE)', [$terms])
                    ->orderBy( 'score', 'desc' )
                    ->orderBy( 'created_at', 'desc' )
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
        return $image;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Image  $image
     *
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
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
            // if mutable, we have to deduplicate the logo and the author text
            'keywords'    => ['sometimes', 'nullable', new ImmutableRule($image)],
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
            'keywords'    => ['string', 'nullable'],
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
     * @return void
     */
    private function generateThumbnail(Image $image)
    {
        try {
            $image->generateThumbnail();
        } catch (\ImagickException | ThumbnailException $e) {
            Log::error('Failed to generate thumbnail. File: '.$image->filename);

            return abort(500,'Failed to generate thumbnail.');
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

    /**
     * Do only search word characters and allow partial matches (word start)
     *
     * @param string $string
     *
     * @return string|null
     */
    private function prepareTerms( string $string ): ?string {
        $query = '';

        // quoted strings must be treated differently
        // so extract them first
        if ( preg_match_all( '/([\+\-]?".+")/U', $string, $quoted ) ) {
            $query  .= implode( ' ', $quoted[0] ) . ' ';
            $string = trim( str_replace( $quoted[0], '', $string ) );
        }

        // terms with quantifiers can't have a wildcard either (innoDB 5.7)
        // so treat them separately as well
        if ( preg_match_all( '/([\+\-][\w\.]+)/u', $string, $quantified ) ) {
            $query  .= implode( ' ', $quantified[0] ) . ' ';
            $string = trim( str_replace( $quantified[0], '', $string ) );
        }

        // if there are unquoted parts left, process them for partial matches
        if ( $string ) {
            $terms = preg_split( "/[^\w\+\-\.]+/Uu", $string, 0, PREG_SPLIT_NO_EMPTY );
            if ( ! $terms ) {
                return $query;
            }
            $query .= implode( '* ', $terms ) . '*';
        }

        return $query;
    }
}
