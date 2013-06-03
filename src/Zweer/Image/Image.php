<?php

namespace Zweer\Image;

class Image
{
    const ORIENTATION_LANDSCAPE = 'landscape';
    const ORIENTATION_PORTRAIT  = 'portrait';
    const ORIENTATION_SQUARE    = 'square';

    const FORMAT_GIF            = 'gif';
    const FORMAT_JPEG           = 'jpeg';
    const FORMAT_PNG            = 'png';

    const LIBRARY_GD            = 'gd';
    const LIBRARY_GMAGICK       = 'gmagick';
    const LIBRARY_IMAGICK       = 'imagick';

    public static function make($filename, $library = null)
    {
        if (is_null($library)) {
            $library = static::LIBRARY_GD;
        }

        $class = '\\Zweer\\Image\\Driver\\' . ucfirst($library) . '\\Image';
        return new $class($filename);
    }

    public static function create($width, $height = null, $bgColor = null, $library = null)
    {
        if (is_null($library)) {
            $library = static::LIBRARY_GD;
        }

        $class = '\\Zweer\\Image\\Driver\\' . ucfirst($library) . '\\Image';
        return new $class(null, $width, $height, $bgColor);
    }
}