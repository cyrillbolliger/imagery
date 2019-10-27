<?php


namespace App\Domain;


use App\Exceptions\UploadException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
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
            Storage::setVisibility($this->relDirPath, 'private');
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
     * @param  string  $filename
     * @param  int  $part
     *
     * @throws UploadException
     */
    public function saveChunk(string $chunk, string $filename, int $part): void
    {
        $base64  = $this->extractData($chunk);
        $newData = $this->base64decode($base64);

        $relFilePath = $this->relDirPath.DIRECTORY_SEPARATOR.$filename;

        // don't use laravel's Storage::append() function because it adds a
        // newline character in between.
        if (0 === $part) {
            $data = $newData;
        } else {
            if ( ! Storage::exists($relFilePath)) {
                throw new UploadException('Invalid part number.', UploadException::PART);
            }

            $existingData = Storage::get($relFilePath);
            $data         = $existingData.$newData;
        }

        if ($this->isSizeLimitExceeded($data)) {
            throw new UploadException('Size limit exceeded.', UploadException::SIZE);
        }

        $written = Storage::put($relFilePath, $data, 'private');

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

    /**
     * Check if the given file exceeds the configured max file size
     *
     * @param  string  $data
     *
     * @return bool
     */
    private function isSizeLimitExceeded(string $data)
    {
        $sizeLimit = config('app.uploads_max_file_size') * 1024 * 1024; // config in MB but we need bytes

        return strlen($data) > $sizeLimit;
    }

    /**
     * Generate temporary filename that is deterministic but hard to guess.
     *
     * @param  string  $originalFilename
     *
     * @return string
     */
    public static function computeTmpFilename(string $originalFilename)
    {
        // Bind filename to user id so we don't interfere with other users
        // uploading a file with the same name.
        $hash = md5($originalFilename.Auth::id().config('salt'));

        return $hash;
    }

    /**
     * Check if the mime type of the given file is in the whitelist
     *
     * @param  string  $relFilePath
     * @param  array  $allowedMimeTypes
     *
     * @return bool
     */
    public static function validateMimeType(string $relFilePath, array $allowedMimeTypes): bool
    {
        $mime = Storage::mimeType($relFilePath);

        return in_array($mime, $allowedMimeTypes);
    }

    /**
     * Generate filename that depends on the file content but is hard to guess.
     *
     * Use the file hash as file name base, so we can prevent storing duplicates.
     *
     * @param  string  $relTmpFilePath
     *
     * @return string
     */
    public static function computeFinalFilename(string $relTmpFilePath)
    {
        $path = disk_path($relTmpFilePath);

        return md5(File::hash($path).config('salt'));
    }
}
