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
     * @param  Request  $request
     * @param  Logo  $logo
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Logo $logo)
    {
        $data = $request->validate([
            'id'         => ['sometimes', new ImmutableRule($logo)],
            'added_by'   => ['sometimes', new ImmutableRule($logo)],
            'filename'   => ['required', 'string', new FileExtensionRule(self::ALLOWED_EXT)],
            'name'       => ['required', 'max:80'],
            'created_at' => ['sometimes', new ImmutableRule($logo)],
            'updated_at' => ['sometimes', new ImmutableRule($logo)],
            'deleted_at' => ['sometimes', new ImmutableRule($logo)],
        ]);

        $path  = $this->getRelTmpPath($data['filename']);
        $valid = $this->validateFile($path);

        if (true !== $valid) {
            return $valid;
        }

        $data['filename'] = $this->storeFile($path);

        $logo->fill($data);

        if ( ! $logo->save()) {
            return response('Could not save logo.', 500);
        }

        return $logo;
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
     * @param  Request  $request
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

        $path  = $this->getRelTmpPath($data['filename']);
        $valid = $this->validateFile($path);

        if (true !== $valid) {
            return $valid;
        }

        $data['filename'] = $this->storeFile($path);

        if ( ! $logo->update($data)) {
            return response('Could not save logo.', 500);
        }

        return $logo;
    }

    /**
     * Get path to the uploaded temporary file relative to the storage folder
     *
     * @param  string  $filename
     *
     * @return string
     */
    private function getRelTmpPath(string $filename)
    {
        $tempFilename = UploadHandler::computeTmpFilename($filename);

        return UploadHandler::getRelDirPath().DIRECTORY_SEPARATOR.$tempFilename;
    }

    /**
     * Check if the file exists and if the mime type is allowed.
     *
     * @param  string  $relFilePath  path to temporary file relative to storage dir
     *
     * @return bool|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     *         true if the file is valid, else a response with the error message.
     */
    private function validateFile(string $relFilePath)
    {
        if ( ! Storage::exists($relFilePath)) {
            return response('Uploaded file not found.', 400);
        }

        if ( ! UploadHandler::validateMimeType($relFilePath, self::ALLOWED_MIME)) {
            $resp = [
                'message' => 'Unable to update logo.',
                'errors'  => [
                    'file' => 'The uploaded file has an invalid mime type'
                ],
            ];

            return response($resp, 422);
        }

        return true;
    }

    /**
     * Store the final file.
     *
     * @param  string  $relFilePath  path to temporary file relative to storage dir
     *
     * @return string  the final file name
     */
    private function storeFile(string $relFilePath)
    {
        $extension      = pathinfo($relFilePath, PATHINFO_EXTENSION);
        $finalFilename  = UploadHandler::computeFinalFilename($relFilePath);
        $relDestDirPath = Logo::getStorageDir().DIRECTORY_SEPARATOR;

        $relFinalPath = $relDestDirPath.$finalFilename.'.'.$extension;

        if ( ! Storage::exists($relFinalPath)) {
            Storage::move($relFilePath, $relFinalPath);
        }

        return $finalFilename.'.'.$extension;
    }

    /**
     * Remove the specified resource from storage.
     *
     * Since it is a soft delete, we do not delete the file.
     *
     * @param  \App\Logo  $logo
     *
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function destroy(Logo $logo)
    {
        if ( ! $logo->delete()) {
            return response('Could not delete logo.', 500);
        }

        return response(null, 204);
    }
}
