<?php

namespace Zweer\Image\Driver\Gd;

use Zweer\Image\Driver\ImageAbstract;
use Zweer\Image\Driver\ImageInterface;

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
     * Initializes the image from a binary string
     * It uses the abstract method to check if the argument is a valid binary string.
     *
     * @abstract
     *
     * @param $binary
     *
     * @throws \InvalidArgumentException
     */
    public function initFromBinary($binary)
    {
        parent::initFromBinary($binary);

        $this->_resource = imagecreatefromstring($binary);
    }

    /**
     * Initializes the image from a path
     * It uses the abstract method to check if the argument is a valid image path.
     *
     * @abstract
     *
     * @param string $filename
     *
     * @throws \InvalidArgumentException
     */
    public function initFromPath($filename)
    {
        parent::initFromPath($filename);

        $info = getimagesize($filename);

        switch ($info[2]) {
            case IMG_PNG:
                $this->_resource = imagecreatefrompng($filename);
                $this->_format = IMG_PNG;
                break;

            case IMG_JPG:
                $this->_resource = imagecreatefromjpeg($filename);
                $this->_format = IMG_JPG;
                break;

            case IMG_GIF:
                $this->_resource = imagecreatefromgif($filename);
                $this->_format = IMG_GIF;
                break;

            default:
                throw new \InvalidArgumentException('The image provided is not of a supported format: ' . var_dump($info));
        }

        $this->_filename = $filename;
    }

    /**
     * Saves the current image
     * If the filename is not specified it takes the original filename (if one).
     * With PNGs and JPEGs the quality attribute states the image quality.
     * It uses the abstract method to know if the filename is set.
     *
     * @param string $filename
     * @param int    $quality
     *
     * @return ImageInterface
     * @throws \InvalidArgumentException
     * @throws \Exception
     */
    public function save($filename = null, $quality = null)
    {
        parent::save($filename, $quality);

        if (is_null($filename)) {
            $filename = $this->_filename;
        }

        $format = substr(strrchr($filename, '.'), 1);
        switch ($format) {
            case 'png':
                if (is_null($quality)) {
                    $quality = 9;
                }

                $result = imagepng($this->_resource, $filename, min(9, max(0, $quality)));
                break;

            case 'jpg':
            case 'jpeg':
                if (is_null($quality)) {
                    $quality = 85;
                }

                $result = imagejpeg($this->_resource, $filename, min(100, max(0, $quality)));
                break;

            case 'gif':
                $result = imagegif($this->_resource, $filename);
                break;

            default:
                throw new \InvalidArgumentException('The format specified is not supported: ' . $format);
        }

        if (!$result) {
            throw new \Exception('unable to save the image: ' . $filename);
        }

        return $this;
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
     * Retrieves the width of the image
     *
     * @return int
     */
    public function getWidth()
    {
        return imagesx($this->_resource);
    }

    /**
     * Retrieves the height of the image
     *
     * @return int
     */
    public function getHeight()
    {
        return imagesy($this->_resource);
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
}