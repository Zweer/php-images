<?php

namespace Zweer\Image\Driver\Gd;

use Zweer\Image\Driver\ImageAbstract;
use Zweer\Image\Driver\ImageInterface;
use Zweer\Image\Driver\ManipulateInterface;

class Image extends ImageAbstract
{
    /**
     * Destroys the current image resource, freeing space
     */
    public function __destruct()
    {
        if (isset($this->_resource)) {
            imagedestroy($this->_resource);
        }
    }

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
    public function initEmpty($width, $height = null, $bgColor = null)
    {
        $this->_resource = static::createBlank($width, $height);

        if (!isset($bgColor)) {
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

        if (!isset($filename)) {
            $filename = $this->_filename;
        }

        $format = substr(strrchr($filename, '.'), 1);
        switch ($format) {
            case 'png':
                if (!isset($quality)) {
                    $quality = 9;
                }

                $result = imagepng($this->_resource, $filename, min(9, max(0, $quality)));
                break;

            case 'jpg':
            case 'jpeg':
                if (!isset($quality)) {
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
     * Outputs the image to the stdout
     *
     * @param int  $format
     * @param int  $quality
     * @param bool $header
     *
     * @return string The mime type of the outputted image
     * @throws \InvalidArgumentException
     * @throws \Exception
     */
    public function output($format = null, $quality = null, $header = true)
    {
        if (!isset($format)) {
            if (!isset($this->_format)) {
                $format = IMG_PNG;
            } else {
                $format = $this->_format;
            }
        }

        if (!isset($quality)) {
            $quality = 90;
        }

        if ($quality < 0 or $quality > 100) {
            throw new \Exception('Output quality must be between 0 and 100, "' . $quality . '" provided');
        }

        switch ($format) {
            case IMG_GIF:
                if ($header) {
                    header('Content-type: image/gif');
                }

                imagegif($this->_resource);
                break;

            case IMG_JPG:
                if ($header) {
                    header('Content-type: image/jpeg');
                }

                imagejpeg($this->_resource, null, $quality);
                break;

            case IMG_PNG:
                if ($header) {
                    header('Content-type: image/png');
                }

                // Transform the quality into the PNG dependant quality [0-9]
                $quality = round($quality / 11.11111111);
                imagepng($this->_resource, null, $quality);
                break;

            default:
                throw new \InvalidArgumentException('The format specified is not supported: ' . $format);
        }

        return image_type_to_mime_type($format);
    }

    /**
     * Allocates the $color in the current image
     *
     * @param array|string $color
     *
     * @return int The color identifier
     */
    public function allocateColor($color)
    {
        list($red, $green, $blue, $alpha) = static::parseColor($color);

        return imagecolorallocatealpha($this->_resource, $red, $green, $blue, $alpha);
    }

    /**
     * Fills the image with $color
     * Before filling, it allocates the color.
     * If no argument is provided, it fills with a transparent color.
     *
     * @see allocateColor()
     *
     * @param array|string $color
     *
     * @return ImageInterface
     */
    public function fill($color = 'f000')
    {
        $color = $this->allocateColor($color);

        imagefill($this->_resource, 0, 0, $color);

        return $this;
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
     * Retrieves the manipulate engine
     *
     * @return ManipulateInterface
     */
    public function manipulate()
    {
        if (!isset($this->_manipulate)) {
            $this->_manipulate = new Manipulate($this);
        }

        return $this->_manipulate;
    }

    /**
     * Copies an $image into the current image
     *
     * @param ImageInterface $image
     * @param int            $destinationX
     * @param int            $destinationY
     * @param int            $sourceX
     * @param int            $sourceY
     * @param int            $destinationWidth
     * @param int            $destinationHeight
     * @param int            $sourceWidth
     * @param int            $sourceHeight
     *
     * @return ImageInterface
     */
    public function copy(ImageInterface $image, $destinationX, $destinationY, $sourceX, $sourceY, $destinationWidth, $destinationHeight, $sourceWidth, $sourceHeight)
    {
        imagecopyresampled($this->_resource, $image->getResource(), $destinationX, $destinationY, $sourceX, $sourceY, $destinationWidth, $destinationHeight, $sourceWidth, $sourceHeight);

        return $this;
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
        return parent::isImageResource($resource) and get_resource_type($resource) == 'gd';
    }

    /**
     * Creates a blank image
     *
     * @param int $width
     * @param int $height
     *
     * @return resource
     */
    public static function createBlank($width, $height = null)
    {
        list($width, $height) = static::parseDimensions($width, $height);

        $new = imagecreatetruecolor($width, $height);

        // preserve transparency
        imagealphablending($new, false);
        imagesavealpha($new, true);

        return $new;
    }
}