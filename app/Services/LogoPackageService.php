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
    private array $zipFiles;

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

        if (!$forceRecreate && Storage::exists($path)) {
            return $path;
        }

        $this->createPackageDir();
        $this->addTemplateFiles();

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

            $pngName = $this->getNiceLogoFilename($colorScheme, 'sRGB', 'png');

            $this->zipFiles[$pngName] = $generator->getPng($this->width, config('app.debug'));
        }
    }

    /**
     * Get unique but a human readable filename without clutter
     *
     * @param  string  $colorScheme
     * @param  string  $ext  file extension
     * @param  string  $colorSpace
     * @return string
     */
    private function getNiceLogoFilename(string $colorScheme, string $colorSpace, string $ext): string
    {
        return sprintf(
            'logo-%s-%s-%s-%s.%s',
            $this->logo->type,
            $this->logo->getSlug(),
            $colorScheme,
            $colorSpace,
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
        $zipFile = $this->relToAbsPath($this->getPackagePath());

        $zip = new \ZipArchive();

        $open = $zip->open($zipFile, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);
        if (!$open) {
            return false;
        }

        $baseDirName = 'logo-'.$this->logo->getSlug();
        foreach ($this->zipFiles as $zipPath => $fsPath) {
            $added = $zip->addFile($this->relToAbsPath($fsPath), "$baseDirName/$zipPath");
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

    private function addTemplateFiles(): void
    {
        $rootPath = $this->relToAbsPath(config('app.logo_template_path'));

        /** @var \SplFileInfo[] $files */
        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($rootPath),
            \RecursiveIteratorIterator::LEAVES_ONLY
        );

        foreach ($files as $file) {
            // Skip directories (they will be added automatically)
            if (!$file->isDir()) {
                $path                  = $this->absToRelPath($file);
                $this->zipFiles[$path] = $path;
            }
        }
    }

    private function absToRelPath(string $absPath): string
    {
        $diskPath = rtrim(disk_path(), DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;
        return str_replace($diskPath, '', $absPath);
    }

    private function relToAbsPath(string $relPath): string
    {
        return disk_path(ltrim($relPath, DIRECTORY_SEPARATOR));
    }
}
