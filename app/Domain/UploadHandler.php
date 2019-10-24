<?php


namespace App\Domain;


use App\Exceptions\UploadException;
use Illuminate\Support\Facades\Storage;

class UploadHandler
{
    /**
     * The upload directory
     *
     * @var string
     */
    private $relDirPath;

    public function __construct()
    {
        $this->relDirPath = self::getRelDirPath();
        $this->createUploadDir();
        $this->removeOldChunks();
    }

    /**
     * Upload directory path relative to the storage root
     *
     * @return string
     */
    public static function getRelDirPath()
    {
        return trim(config('app.uploads_dir'), '/');
    }

    /**
     * Creates the upload directory if it doesnt exist
     */
    private function createUploadDir(): void
    {
        if ( ! Storage::exists($this->relDirPath)) {
            Storage::makeDirectory($this->relDirPath);
        }
    }

    /**
     * Remove chunks without change for more than uploads_ttl seconds
     */
    private function removeOldChunks(): void
    {
        $files  = Storage::allFiles($this->relDirPath);
        $maxAge = time() - config('app.uploads_ttl');

        foreach ($files as $file) {
            if (Storage::exists($file) && Storage::lastModified($file) < $maxAge) {
                Storage::delete($file);
            }
        }
    }

    /**
     * Save the file chunk
     *
     * @param  string  $chunk  base64 encoded
     * @param  string  $fileName
     * @param  int  $part
     *
     * @throws UploadException
     */
    public function saveChunk(string $chunk, string $fileName, int $part): void
    {
        $base64  = $this->extractData($chunk);
        $newData = $this->base64decode($base64);

        $relFilePath = $this->relDirPath.DIRECTORY_SEPARATOR.$fileName;

        // don't use laravel's Storage::append() function because it adds a
        // newline character in between.
        if (0 === $part) {
            $existingData = '';
        } else {
            $existingData = Storage::get($relFilePath);
        }

        $written = Storage::put($relFilePath, $existingData.$newData, 'private');

        if (false === $written) {
            throw new UploadException('Unable to store file.', UploadException::STORE);
        }
    }

    /**
     * Get the base64 encoded data from the given chunk
     *
     * @param  string  $chunk
     *
     * @return string
     * @throws UploadException
     */
    private function extractData(string $chunk): string
    {
        // the file is the part after the last comma
        $start = strrpos($chunk, ',') + 1;

        if (false === $start) {
            throw new UploadException('Invalid data format.', UploadException::DATA_FORMAT);
        }

        return substr($chunk, $start);
    }

    /**
     * Binary string with the base64 decoded data
     *
     * @param  string  $base64
     *
     * @return string
     * @throws UploadException
     */
    private function base64decode(string $base64): string
    {
        $data = base64_decode($base64, true);

        if (false === $data) {
            throw new UploadException('Unable to decode base64 encoded data.', UploadException::BASE64);
        }

        return $data;
    }
}
