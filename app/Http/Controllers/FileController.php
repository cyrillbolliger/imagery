<?php

namespace App\Http\Controllers;

use App\FileModel;
use App\Http\Controllers\Upload\ChunkUploadStrategy;
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
     * @return void
     */
    public function storeChunk(Request $request)
    {
        $handler = new ChunkUploadStrategy(self::ALLOWED_EXT);
        $handler->storeTmp($request);
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
        return $this->fileResponse($model->getRelPath());
    }

    /**
     * Display the specified resource file.
     *
     * @param  FileModel  $model
     *
     * @return Response
     */
    public function showThumbnail(FileModel $model)
    {
        return $this->fileResponse($model->getRelThumbPath());
    }

    /**
     * Respond with file, if it exists, else with a 404
     *
     * @param  string  $relPath
     *
     * @return Response
     */
    private function fileResponse(string $relPath)
    {
        if ( ! Storage::exists($relPath)) {
            return response('File not found', 404);
        }

        return response()->file(disk_path($relPath));
    }
}
