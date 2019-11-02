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
        $path = $model->getRelPath();

        if ( ! Storage::exists($path)) {
            return response('File not found', 404);
        }

        return response()->file(disk_path($path));
    }
}
