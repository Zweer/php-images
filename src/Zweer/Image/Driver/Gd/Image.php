<?php

namespace Zweer\Image\Driver\Gd;

use Zweer\Image\Driver\ImageAbstract;

class Image extends ImageAbstract
{
    /**
     * Initializes an empty image
     * It uses the abstract method to check the dimensions.
     * If the $bgColor is not specified, the image is filled with a transparent
     * layer.
     *
     * @param int          $width   The width of the new empty image
     * @param int          $height  The height of the new empty image
     * @param array|string $bgColor The color to use for the background of the image
     */
    public function initEmpty(&$width, &$height = null, &$bgColor = null)
    {
        parent::initEmpty($width, $height, $bgColor);

        $this->_resource = imagecreatetruecolor($width, $height);

        if (is_null($bgColor)) {
            $bgColor = imagecolorallocatealpha($this->_resource, 0, 0, 0, 127);
        } else {
            $bgColor = $this->parseColor($bgColor);
        }

        imagefill($this->_resource, 0, 0, $bgColor);
    }

    /**
     * Color parser
     * Use the abstract function to parse the rgba value and then allocates
     * the color in the image.
     *
     * @param array|string $color The color to parse
     *
     * @return null
     * @throws \InvalidArgumentException
     */
    public function parseColor($color)
    {
        $red = null;
        $green = null;
        $blue = null;
        $alpha = 0;

        parent::parseColor($color, $red, $green, $blue, $alpha);

        return imagecolorallocatealpha($this->_resource, $red, $green, $blue, $alpha);
    }

    /**
     * The parameter is an image identifier?
     * Uses the abstract function to understand if the argument is a resource
     * and then states if it's a gd resource
     *
     * @param $resource
     *
     * @return bool
     */
    public static function isImageResource($resource)
    {
        return parent::isImageResource($resource) && get_resource_type($resource) == 'gd';
    }

    public function initFromResource($resource)
    {
        // TODO: Implement initFromResource() method.
    }

    public function initFromBinary($binary)
    {
        // TODO: Implement initFromBinary() method.
    }

    public function initFromPath($filename)
    {
        // TODO: Implement initFromPath() method.
    }
}