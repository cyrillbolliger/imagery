<?php


namespace App\Logo;


use App\Exceptions\LogoException;
use Illuminate\Support\Facades\Storage;
use Imagick;
use ImagickException;

trait VectorImageLoaderTrait
{
    /**
     * Load the given vector file into an image magick object with $width.
     *
     * @param  string  $path
     * @param  int  $width
     * @return Imagick
     * @throws LogoException
     */
    private function imFromVectorFile(string $path, int $width): Imagick
    {
        if (!Storage::exists($path)) {
            throw new LogoException('Vector image not found: '.$path);
        }
        $absPath = disk_path($path);

        $initialResolution = 100;

        $im = new Imagick();
        $im->setBackgroundColor('transparent');
        $im->setResolution(
            $initialResolution,
            $initialResolution
        );

        try {
            // read image to determine and set pixel density so the svg will be
            // rastered to the correct size (when loading it the second time).
            $im->readImage($absPath);
            $im->trimImage(0);
            $resolutionRatio  = $initialResolution / $im->getImageWidth();
            $targetResolution = $resolutionRatio * $width;
            $im->removeImage();
            $im->setResolution($targetResolution, $targetResolution);

            // reload the image, now with the correct pixel density
            $im->readImage($absPath);
        } catch (ImagickException $e) {
            throw new LogoException("Image magick can't read base logo: $e");
        }

        // cut borders
        $im->trimImage(0);

        return $im;
    }
}
