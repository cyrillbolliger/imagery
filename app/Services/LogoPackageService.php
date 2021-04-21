<?php


namespace App\Services;


use App\Exceptions\LogoException;
use App\Logo;
use App\Logo\LogoFactory;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class LogoPackageService
{
    private const FILE_EXT = 'zip';

    private int $width;
    private Logo $logo;
    private array $screenPaths;
    private array $printPaths;

    public function __construct()
    {
        $this->width = (int) config('app.logo_width');
    }

    /**
     * Create all logos in all variants and package them into a zip file
     *
     * @param  Logo  $logo
     * @param  bool  $forceRecreate
     *
     * @return string the relative path to the package zip
     */
    public function generatePackage(Logo $logo, bool $forceRecreate = false): string
    {
        $this->logo = $logo;
        $path       = $this->getPackagePath();

        if (! $forceRecreate && Storage::exists($path)) {
            return $path;
        }

        $this->createPackageDir();

        try {
            $this->generateLogos();
        } catch (LogoException $e) {
            Log::error($e->getMessage());
            abort(500, 'Failed to create logo.');
        }

        $success = $this->makeZip();

        if (!$success) {
            abort(500, 'Failed to create zip.');
        }

        return $path;
    }

    /**
     * Generate deterministic path to the package
     *
     * @return string relative path
     */
    private function getPackagePath(): string
    {
        $path     = $this->getPackageDirPath();
        $filename = $this->logo->id.'.'.self::FILE_EXT;

        return $path.DIRECTORY_SEPARATOR.$filename;
    }

    /**
     * The relative path to the directory that stores the packages.
     *
     * @return string relative path
     */
    private function getPackageDirPath(): string
    {
        return trim(config('app.packages_dir'), '/');
    }

    /**
     * Creates the package directory if it doesn't exist
     */
    private function createPackageDir(): void
    {
        $path = $this->getPackageDirPath();

        if (!Storage::exists($path)) {
            Storage::makeDirectory($path);
            Storage::setVisibility($path, 'private');
        }
    }

    /**
     * @throws \App\Exceptions\LogoException
     */
    private function generateLogos(): void
    {
        $colorSchemes = [
            \App\Logo\Logo::LOGO_COLOR_LIGHT,
            \App\Logo\Logo::LOGO_COLOR_DARK
        ];

        foreach ($colorSchemes as $colorScheme) {
            $generator = LogoFactory::get(
                $this->logo->type,
                $colorScheme,
                [$this->logo->name]
            );

            $pngName = $this->getNiceLogoFilename($colorScheme, 'png');
            $psdName = $this->getNiceLogoFilename($colorScheme, 'psd');

            $this->screenPaths[$pngName] = $generator->getPng($this->width, config('app.debug'));
            $this->printPaths[$psdName]  = $generator->getPsd($this->width, config('app.debug'));
        }
    }

    /**
     * Get unique but a human readable filename without clutter
     *
     * @param  string  $colorScheme
     * @param  string  $ext  file extension
     * @return string
     */
    private function getNiceLogoFilename(string $colorScheme, string $ext): string
    {
        $name = mb_strtolower($this->logo->name);
        $name = str_replace(['ä', 'ö', 'ü'], ['ae', 'oe', 'ue'], $name);
        $name = preg_replace('/\.ch$/', '', $name);
        $name = Str::slug($name); // removes any bad chars

        return sprintf(
            'logo-%s-%s-%s.%s',
            (string) $this->logo->type,
            $name,
            $colorScheme,
            $ext
        );
    }

    /**
     * Package the files in screenPath and printPath into a zip file.
     *
     * @return bool true for success, false otherwise
     */
    private function makeZip(): bool
    {
        $zipFile = disk_path($this->getPackagePath());

        $zip = new \ZipArchive();

        $open = $zip->open($zipFile, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);
        if (!$open) {
            return false;
        }

        foreach ($this->printPaths as $filename => $path) {
            $added = $zip->addFile(disk_path($path), "logo/print/$filename");
            if (!$added) {
                return false;
            }
        }

        foreach ($this->screenPaths as $filename => $path) {
            $added = $zip->addFile(disk_path($path), "logo/screen/$filename");
            if (!$added) {
                return false;
            }
        }

        $closed = $zip->close();
        if (!$closed) {
            return false;
        }

        return true;
    }
}
