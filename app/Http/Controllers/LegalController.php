<?php

namespace App\Http\Controllers;

use App\Image;
use App\Legal;
use App\Rules\ImmutableRule;
use Illuminate\Http\Request;

class LegalController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Image  $image
     * @param  Legal  $legal
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Image $image, Legal $legal)
    {
        if ($image->legal) {
            return response('The legal for this image already exists.', 409);
        }

        $data = $request->validate([
            'id'                   => ['sometimes', new ImmutableRule($legal)],
            'image_id'             => [
                'sometimes',
                'exists:images,id',
                new ImmutableRule($legal),
            ],
            'right_of_personality' => [
                'required',
                'in:'.Legal::PERSONALITY_NOT_APPLICABLE.','
                .Legal::PERSONALITY_AGREEMENT.','
                .Legal::PERSONALITY_PUBLIC_INTEREST.','
                .Legal::PERSONALITY_UNKNOWN.','
                .Legal::PERSONALITY_NO_AGREEMENT
            ],
            'originator_type'      => [
                'required',
                'in:'.Legal::ORIGINATOR_USER.','
                .Legal::ORIGINATOR_STOCK.','
                .Legal::ORIGINATOR_AGENCY.','
                .Legal::ORIGINATOR_FRIEND.','
                .Legal::ORIGINATOR_UNKNOWN
            ],
            'originator'           => [
                'required_unless:originator_type,'.Legal::ORIGINATOR_UNKNOWN,
                'max:192'
            ],
            'licence'              => [
                'required_if:originator_type,'.Legal::ORIGINATOR_STOCK,
                'in:'.Legal::LICENCE_CC.','
                .Legal::LICENCE_CC_ATTRIBUTION.','
                .Legal::LICENCE_OTHER
            ],
            'stock_url'            => [
                'required_if:originator_type,'.Legal::ORIGINATOR_STOCK,
                'max:2048',
                'url'
            ],
            'shared'               => ['required', 'boolean'],
            'created_at'           => ['sometimes', new ImmutableRule($legal)],
            'updated_at'           => ['sometimes', new ImmutableRule($legal)],
            'deleted_at'           => ['sometimes', new ImmutableRule($legal)],
        ]);

        $legal->fill($data);

        if ( ! $image->legal()->save($legal)) {
            return response('Could not save legal.', 500);
        }

        return $legal;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Image  $image
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Image $image)
    {
        if ( ! $image->legal) {
            return response('This image has no legal information.', 404);
        }

        return $image->legal;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Image  $legal
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Image $image)
    {
        if ( ! $image->legal) {
            return response('This image has no legal information.', 404);
        }

        $legal = $image->legal;

        $data = $request->validate([
            'id'                   => ['sometimes', new ImmutableRule($legal)],
            'image_id'             => [
                'sometimes',
                'exists:images,id',
                new ImmutableRule($legal),
            ],
            'right_of_personality' => [
                'sometimes',
                'required',
                'in:'.Legal::PERSONALITY_NOT_APPLICABLE.','
                .Legal::PERSONALITY_AGREEMENT.','
                .Legal::PERSONALITY_PUBLIC_INTEREST.','
                .Legal::PERSONALITY_UNKNOWN.','
                .Legal::PERSONALITY_NO_AGREEMENT
            ],
            'originator_type'      => [
                'sometimes',
                'required',
                'in:'.Legal::ORIGINATOR_USER.','
                .Legal::ORIGINATOR_STOCK.','
                .Legal::ORIGINATOR_AGENCY.','
                .Legal::ORIGINATOR_FRIEND.','
                .Legal::ORIGINATOR_UNKNOWN
            ],
            'originator'           => [
                'sometimes',
                'required_unless:originator_type,'.Legal::ORIGINATOR_UNKNOWN,
                'max:192'
            ],
            'licence'              => [
                'sometimes',
                'required_if:originator_type,'.Legal::ORIGINATOR_AGENCY,
                'in:'.Legal::LICENCE_CC.','
                .Legal::LICENCE_CC_ATTRIBUTION.','
                .Legal::LICENCE_OTHER
            ],
            'stock_url'            => [
                'sometimes',
                'required_if:originator_type,'.Legal::ORIGINATOR_AGENCY,
                'max:2048',
                'url'
            ],
            'shared'               => ['sometimes', 'required', 'boolean'],
            'created_at'           => ['sometimes', new ImmutableRule($legal)],
            'updated_at'           => ['sometimes', new ImmutableRule($legal)],
            'deleted_at'           => ['sometimes', new ImmutableRule($legal)],
        ]);

        if ( ! $legal->update($data)) {
            return response('Could not save legal.', 500);
        }

        return $legal;
    }
}
