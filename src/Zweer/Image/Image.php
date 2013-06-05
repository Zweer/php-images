<?php

namespace Zweer\Image;

use Zweer\Image\Driver\ImageInterface;

class Image
{
    const ORIENTATION_LANDSCAPE = 'landscape';
    const ORIENTATION_PORTRAIT  = 'portrait';
    const ORIENTATION_SQUARE    = 'square';

    const LIBRARY_GD            = 'gd';
    const LIBRARY_GMAGICK       = 'gmagick';
    const LIBRARY_IMAGICK       = 'imagick';

    const FLIP_HORIZONTAL       = 'horizontal';
    const FLIP_VERTICAL         = 'vertical';

    /**
     * Creates an image from a file, resource or binary string
     *
     * @param string|resource $filename
     * @param string          $library
     *
     * @return ImageInterface
     */
    public static function make($filename, $library = null)
    {
        if (!isset($library)) {
            $library = static::LIBRARY_GD;
        }

        $class = '\\Zweer\\Image\\Driver\\' . ucfirst($library) . '\\Image';
        return new $class($filename);
    }

    /**
     * Creates an empty image with the specified dimensions and color
     *
     * @param int          $width
     * @param int          $height
     * @param array|string $bgColor
     * @param string       $library
     *
     * @return ImageInterface
     */
    public static function create($width, $height = null, $bgColor = null, $library = null)
    {
        if (!isset($library)) {
            $library = static::LIBRARY_GD;
        }

        $class = '\\Zweer\\Image\\Driver\\' . ucfirst($library) . '\\Image';
        return new $class(null, $width, $height, $bgColor);
    }
}