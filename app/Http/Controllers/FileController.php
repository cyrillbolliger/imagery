<?php

namespace App\Http\Controllers;

use App\Domain\UploadHandler;
use App\Exceptions\UploadException;
use App\FileModel;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class FileController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     *
     * @param  UploadHandler  $handler
     *
     * @return void
     */
    public function store(Request $request, UploadHandler $handler)
    {
        $data     = $request->json('data');
        $part     = $request->json('part');
        $filename = $request->json('filename');

        // keep the filename deterministic but hard to guess (validation is only
        // only made after the file is linked to the database). Bind file name
        // to user id so we don't interfere with other users uploading a file
        // with the same name.
        $tempFileName = hash('sha256', $filename.Auth::id().config('salt'));

        try {
            $handler->saveChunk($data, $tempFileName, $part);
        } catch (UploadException $e) {
            return response($e->getMessage(), 400);
        }
    }

    /**
     * Display the specified resource file.
     *
     * @param  FileModel  $model
     *
     * @return Response
     */
    public function show(FileModel $model)
    {
        $path = $model->getPath();

        if ( ! file_exists($path) || ! is_readable($path)) {
            return response('File not found', 404);
        }

        return response()->file($model->getPath());
    }
}
