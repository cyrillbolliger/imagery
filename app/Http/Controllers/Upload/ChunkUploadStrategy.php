<?php


namespace App\Http\Controllers\Upload;


use App\Exceptions\UploadException;
use App\Rules\FileExtensionRule;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ChunkUploadStrategy extends UploadStrategy
{
    private const KEY_DATA = 'base64data';
    private const KEY_PART = 'part';

    /**
     * The base64 encoded uploaded data
     *
     * @var string
     */
    private $data;

    /**
     * The chunk number
     *
     * @var int
     */
    private $part;

    /**
     * Save the uploaded data as temporary file
     *
     * @param  Request  $request
     *
     * @return void
     */
    public function storeTmp(Request $request): void
    {
        // validate request and initiate state data
        $this->setValidatedData($request);

        // actually save the data
        try {
            $this->saveChunk();
        } catch (UploadException $e) {
            Log::error('Failed to save uploaded chunk. Storage put returned false.');
            abort(500, 'Internal Server Error.');
        }
    }

    /**
     * Validate the request data and move them into the class state
     *
     * @param  Request  $request
     */
    private function setValidatedData(Request $request)
    {
        $data = $request->validate([
            self::KEY_PART     => 'required|integer|min:0',
            self::KEY_FILENAME => [
                'bail',
                'required',
                'string',
                new FileExtensionRule($this->allowedFileExt)
            ],
        ]);

        $this->part     = $data[self::KEY_PART];
        $this->filename = $data[self::KEY_FILENAME];

        // we do need the properties above to be able to validate the data
        $data = $request->validate([
            self::KEY_DATA => [
                'bail',
                'required',
                'string',
                function ($attribute, $value, $fail) {
                    $current  = $this->getCurrentTmpFileSize();
                    $fileMax  = $this->allowedFileSize;
                    $chunkMax = config('app.uploads_max_chunk_size') * 1024 * 1024;

                    $max       = min($fileMax - $current, $chunkMax);
                    $base64max = ceil($max * 4 / 3);

                    $base64value = rtrim($this->extractData($value), '=');

                    if (strlen($base64value) > $base64max) {
                        $this->validationErrorAbort($attribute, 'Max file size exceeded.');
                    }
                },
            ]
        ]);

        $this->data = $data[self::KEY_DATA];
    }

    /**
     * Return the size of the already uploaded chunks of this file.
     *
     * Returns zero if there were no chunks uploaded.
     *
     * @return int
     */
    private function getCurrentTmpFileSize()
    {
        $path = $this->getRelTmpPath($this->filename);

        if ( ! Storage::exists($path)) {
            return 0;
        }

        return Storage::size($path);
    }

    /**
     * Get the base64 encoded data from the given chunk
     *
     * @param  string  $chunk
     *
     * @return string
     */
    private function extractData(string $chunk): string
    {
        // the file is the part after the first comma
        $start = strpos($chunk, ',') + 1;

        if (false === $start) {
            $this->validationErrorAbort(self::KEY_DATA, 'Invalid data format.');
        }

        return substr($chunk, $start);
    }

    /**
     * Save the file chunk
     *
     * @throws UploadException
     */
    private function saveChunk()
    {
        $base64  = $this->extractData($this->data);
        $newData = $this->base64decode($base64);

        $relFilePath = $this->getRelTmpPath($this->filename);

        // don't use laravel's Storage::append() function because it adds a
        // newline character in between.
        if (0 === $this->part) {
            $data = $newData;
        } else {
            if ( ! Storage::exists($relFilePath)) {
                $this->validationErrorAbort(self::KEY_PART, 'Invalid part number.');
            }

            $existingData = Storage::get($relFilePath);
            $data         = $existingData.$newData;
        }

        $written = Storage::put($relFilePath, $data, 'private');

        if (false === $written) {
            throw new UploadException('Unable to store file.');
        }
    }

    /**
     * Binary string with the base64 decoded data
     *
     * @param  string  $base64
     *
     * @return string
     */
    private function base64decode(string $base64): string
    {
        $data = base64_decode($base64, true);

        if (false === $data) {
            $this->validationErrorAbort(self::KEY_DATA, 'Unable to decode base64 encoded data.');
        }

        return $data;
    }
}
