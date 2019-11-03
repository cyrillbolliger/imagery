<?php

namespace App\Http\Controllers;

use App\Legal;
use App\Rules\ImmutableRule;
use Illuminate\Http\Request;

class LegalController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Legal::where('shared', true)
                    ->orderBy('created_at')
                    ->paginate(50);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Legal  $legal
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Legal $legal)
    {
        return $legal;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Legal  $legal
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Legal $legal)
    {
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

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Legal  $legal
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Legal $legal)
    {
        //
    }
}
