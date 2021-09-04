<?php


namespace App\Logo;


use App\Exceptions\LogoException;

class LogoFactory
{
    /**
     * @param  string  $type
     * @param  string  $color
     * @param  array  $args
     * @return Logo
     * @throws LogoException
     */
    public static function get(string $type, string $color, array $args): Logo
    {
        Logo::validateColorScheme($color);

        $compositor = null;
        switch (mb_strtolower($type)) {
            case 'alternative':
                $compositor = new Alternative();
                break;
            case 'alternative-risch':
                $compositor = new AlternativeRisch();
                break;
            case 'gruene':
                $compositor = new Gruene(...$args);
                break;
            case 'gruene-vert-e-s':
                $compositor = new GrueneVertes(...$args);
                break;
            case 'gruene-verts':
                $compositor = new GrueneVerts(...$args);
                break;
            case 'verda':
                $compositor = new Verda(...$args);
                break;
            case 'verdi':
                $compositor = new Verdi(...$args);
                break;
            case 'vert-e-s':
                $compositor = new Vertes(...$args);
                break;
            case 'verts':
                $compositor = new Verts(...$args);
                break;
            default:
                throw new LogoException("Missing logo compositor: $type");
        }

        return new Logo($compositor, $color);
    }
}
