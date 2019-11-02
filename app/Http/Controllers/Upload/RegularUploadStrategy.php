<?php


namespace App\Http\Controllers\Upload;


use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class RegularUploadStrategy extends UploadStrategy
{

    /**
     * Save the uploaded data as temporary file
     *
     * @param  Request  $request
     *
     * @return void
     */
    public function storeTmp(Request $request): void
    {
        $request->validate([
            'file' => [
                'bail',
                'required',
                'file',
                'mimes:'.implode(',', $this->allowedFileExt),
                'max:'.$this->allowedFileSize
            ],
        ]);

        $file = $request->get('file');

        if ( ! $file->isValid()) {
            $this->validationErrorAbort('file', 'Upload error. Please try again.');
        }

        $tmpFilename = $this->computeTmpFilename($file->getClientOriginalName());
        $tmpDirPath  = $this->getRelTmpPath();

        if (Storage::exists($tmpDirPath.DIRECTORY_SEPARATOR.$tmpFilename)) {
            Storage::delete($tmpDirPath.DIRECTORY_SEPARATOR.$tmpFilename);
        }

        if ( ! $file->storeAs($tmpDirPath, $tmpFilename)) {
            Log::error('Failed to save uploaded file.');
            abort(500, 'Internal Server Error.');
        }
    }
}
