<?php


namespace App\Http\Controllers\Upload;


use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

abstract class UploadStrategy
{
    protected const KEY_FILENAME = 'filename';
    private const BIN_EXTENSION = '.bin';

    /**
     * The filename of the uploaded file (differs from tmp and final filename)
     *
     * @var string
     */
    protected $filename;

    /**
     * The allowed file extensions (mime types are derived and checked as well)
     *
     * @var array
     */
    protected $allowedFileExt;

    /**
     * The allowed maximum file size in Byte
     *
     * @var int
     */
    protected $allowedFileSize;

    /**
     * AbstactUploadHandler constructor.
     *
     * @param  array  $allowedFileExt
     * @param  string  $filename  filename of the uploaded file
     * @param  float|null  $allowedFileSize  in MB
     */
    public function __construct(
        array $allowedFileExt,
        string $filename = null,
        float $allowedFileSize = null
    ) {
        if (null === $allowedFileSize) {
            $allowedFileSize = config('app.uploads_max_file_size');
        }

        $this->allowedFileExt  = $allowedFileExt;
        $this->filename        = $filename;
        $this->allowedFileSize = (int) ($allowedFileSize * 1024 * 1024);

        $this->createUploadDir();
        $this->removeOldTmpFiles();
    }

    /**
     * Creates the upload directory if it doesn't exist
     */
    protected function createUploadDir(): void
    {
        $path = $this->getRelTmpPath();

        if ( ! Storage::exists($path)) {
            Storage::makeDirectory($path);
            Storage::setVisibility($path, 'private');
        }
    }

    /**
     * Get path to the uploaded temporary file relative to the storage folder
     * or the uploading folder relative to the storage folder if no filename is
     * provided.
     *
     * @param  string|null  $filename
     *
     * @return string
     */
    protected function getRelTmpPath(string $filename = null)
    {
        $relPath = trim(config('app.uploads_dir'), '/');

        if ($filename) {
            $relPath .= DIRECTORY_SEPARATOR.$this->computeTmpFilename($filename);
        }

        return $relPath;
    }

    /**
     * Generate temporary filename that is deterministic but hard to guess.
     *
     * @param  string  $originalFilename
     *
     * @return string
     */
    protected function computeTmpFilename(string $originalFilename)
    {
        // Bind filename to user id so we don't interfere with other users
        // uploading a file with the same name.
        return self::shortHash($originalFilename.Auth::id().config('app.hash_secret'));
    }

    /**
     * Return a sha256 hash of the given string
     *
     * @param  string  $string
     *
     * @return string
     */
    private static function shortHash(string $string)
    {
        return substr(hash('sha256', $string), 0, 32);
    }

    /**
     * Remove temporary files without change for more than uploads_ttl seconds
     */
    private function removeOldTmpFiles(): void
    {
        $files  = Storage::allFiles($this->getRelTmpPath());
        $maxAge = time() - config('app.uploads_ttl');

        foreach ($files as $file) {
            if (Storage::exists($file) && Storage::lastModified($file) < $maxAge) {
                Storage::delete($file);
            }
        }
    }

    /**
     * Save the uploaded data as temporary file
     *
     * @param  Request  $request
     *
     * @return void
     */
    public abstract function storeTmp(Request $request): void;

    /**
     * Save the uploaded temporary file in the given folder.
     *
     * @param  string  $relDirPath
     *
     * @return string  the file name
     */
    public function storeFinal(string $relDirPath): string
    {
        $this->decodeFile();
        $this->validateFile();

        $extension     = pathinfo($this->filename, PATHINFO_EXTENSION);
        $finalFilename = $this->computeFinalFilename();
        $relDirPath    = trim($relDirPath, '/').DIRECTORY_SEPARATOR;
        $relFinalPath  = $relDirPath.$finalFilename.'.'.$extension;

        $relTmpPath = $this->getRelTmpPath($this->filename).self::BIN_EXTENSION;

        // Since the final file name is based on the file hash
        // we do only have to move the file, if a file with the same
        // hash does not yet exist. This prevents duplication.
        // Remaining files will be deleted by the temporary file garbage
        // collector.
        if ( ! Storage::exists($relFinalPath)) {
            Storage::move($relTmpPath, $relFinalPath);
            Storage::setVisibility($relFinalPath, 'private');
        }

        return $finalFilename.'.'.$extension;
    }

    /**
     * Abort if the file doesn't exists of if the mime type is not allowed
     */
    private function validateFile()
    {
        $relTmpPath = $this->getRelTmpPath($this->filename).self::BIN_EXTENSION;

        if ( ! Storage::exists($relTmpPath)) {
            $this->validationErrorAbort(self::KEY_FILENAME, 'Decoded file not found.');
        }

        $mimeType  = Storage::mimeType($relTmpPath);
        $converter = new \Mimey\MimeTypes;

        $mimeExt = $converter->getExtension($mimeType);
        if ( ! in_array($mimeExt, $this->allowedFileExt)) {
            $this->validationErrorAbort('file', 'The uploaded file has an invalid mime type.');
        }
    }

    /**
     * Abort execution due to a validation error and respond with message
     *
     * @param  string  $field  the field that was responsible for the error
     * @param  string  $message  the field specific error message
     */
    protected function validationErrorAbort(string $field, string $message)
    {
        $resp = [
            'message' => 'Upload failed.',
            'errors'  => [
                $field => [$message]
            ],
        ];

        abort(response($resp, 422));
    }

    /**
     * Generate filename that depends on the file content but is hard to guess.
     *
     * Use the file hash as file name base, so we can prevent storing
     * duplicates.
     *
     * @return string
     */
    private function computeFinalFilename()
    {
        $path = disk_path($this->getRelTmpPath($this->filename));

        return self::shortHash(File::hash($path).config('app.hash_secret'));
    }

    /**
     * Decode the base64 decoded file and save it on the same spot but as .bin
     *
     * @return void
     */
    private function decodeFile(): void
    {
        $relTmpPath = $this->getRelTmpPath($this->filename);

        if ( ! Storage::exists($relTmpPath)) {
            $this->validationErrorAbort(self::KEY_FILENAME, 'Uploaded file not found.');
        }

        $fileContents = Storage::get($relTmpPath);
        $base64       = str_replace(' ', '+', $fileContents); // https://www.php.net/manual/de/function.base64-decode.php#102113
        $data         = base64_decode($base64, true);

        Storage::put($relTmpPath.self::BIN_EXTENSION, $data, 'private');
    }
}
