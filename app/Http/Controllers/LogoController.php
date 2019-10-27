<?php

namespace App\Http\Controllers;

use App\Domain\UploadHandler;
use App\Logo;
use App\Rules\FileExtensionRule;
use App\Rules\ImmutableRule;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class LogoController extends Controller
{
    private const ALLOWED_MIME = ['image/png', 'image/svg+xml'];
    private const ALLOWED_EXT = ['png', 'svg'];

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        /** @var User $user */
        $user = Auth::user();

        return $user->manageableLogos();
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
     * @param  \App\Logo  $logo
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Logo $logo)
    {
        return $logo;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Logo  $logo
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Logo $logo)
    {
        $data = $request->validate([
            'id'         => ['sometimes', new ImmutableRule($logo)],
            'added_by'   => ['sometimes', new ImmutableRule($logo)],
            'filename'   => ['required', 'string', new FileExtensionRule(self::ALLOWED_EXT)],
            'name'       => ['sometimes', 'required', 'max:80'],
            'created_at' => ['sometimes', new ImmutableRule($logo)],
            'updated_at' => ['sometimes', new ImmutableRule($logo)],
            'deleted_at' => ['sometimes', new ImmutableRule($logo)],
        ]);

        $tempFilename   = UploadHandler::computeTmpFilename($data['filename']);
        $relTmpFilePath = UploadHandler::getRelDirPath().DIRECTORY_SEPARATOR.$tempFilename;

        if ( ! Storage::exists($relTmpFilePath)) {
            return response('Uploaded file not found.', 400);
        }

        if ( ! UploadHandler::validateMimeType($relTmpFilePath, self::ALLOWED_MIME)) {
            return response('Invalid mime type.', 422);
        }

        $extension      = pathinfo($data['filename'], PATHINFO_EXTENSION);
        $finalFilename  = UploadHandler::computeFinalFilename($relTmpFilePath);
        $relDestDirPath = config('app.logo_dir').DIRECTORY_SEPARATOR;

        $relFinalPath = $relDestDirPath.$finalFilename.'.'.$extension;

        if ( ! Storage::exists($relFinalPath)) {
            Storage::move($relTmpFilePath, $relFinalPath);
        }

        $data['filename'] = $finalFilename.'.'.$extension;

        if ( ! $logo->update($data)) {
            return response('Could not save logo.', 500);
        }

        return $logo;
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Logo  $logo
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Logo $logo)
    {
        //
    }
}
