<?php

namespace App\Http\Controllers;

use App\Domain\UploadHandler;
use App\Exceptions\UploadException;
use App\FileModel;
use App\Rules\FileExtensionRule;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

class FileController extends Controller
{
    private const ALLOWED_EXT = ['jpg', 'jpeg', 'png', 'svg'];

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     *
     * @param  UploadHandler  $handler
     *
     * @return void
     */
    public function storeChunk(Request $request, UploadHandler $handler)
    {
        $data = $request->validate([
            'base64data' => 'required|string',
            'part'       => 'required|integer|min:0',
            'filename'   => ['required', 'string', new FileExtensionRule(self::ALLOWED_EXT)],
        ]);

        $tempFileName = UploadHandler::computeTmpFilename($data['filename']);

        try {
            $handler->saveChunk($data['base64data'], $tempFileName, $data['part']);
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
        $path = $model->getRelPath();

        if ( ! Storage::exists($path)) {
            return response('File not found', 404);
        }

        return response()->file(disk_path($path));
    }
}
