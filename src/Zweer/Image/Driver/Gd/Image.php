<?php

namespace Zweer\Image\Driver\Gd;

use Zweer\Image\Image\ImageAbstract;
use Zweer\Image\Image\ImageInterface;
use Zweer\Image\Manipulate\ManipulateInterface;
use Zweer\Image\Effect\EffectInterface;
use Zweer\Image\Draw\DrawInterface;

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

        if (isset($bgColor)) {
            $this->fill($bgColor);
        } else {
            $this->fill();
        }
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
     * Pick the color at ($x, $y)
     *
     * @param int    $x
     * @param int    $y
     * @param string $format
     *
     * @throws \InvalidArgumentException
     * @return string|int|array
     */
    public function pickColor($x, $y, $format = 'array')
    {
        return static::formatColor(imagecolorat($this->_resource, $x, $y), $format);
    }

    /**
     * Retrieves all the colors of the image
     * If $format is null, it returns the integer representation
     * Doesn't work for 24bit images!!!
     *
     * @param string $format
     *
     * @return string[]|int[]|array[]
     */
    public function pickColors($format = null)
    {
        $colors = array();
        $num = imagecolorstotal($this->_resource);

        for ($i = 0; $i < $num; ++$i) {
            $color = imagecolorsforindex($this->_resource, $i);
            $colors[] = isset($format) ? static::formatColor($color, $format) : $color;
        }

        return $colors;
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
     * Sets the $index color
     *
     * @param int $index
     * @param int $red
     * @param int $green
     * @param int $blue
     * @param int $alpha
     *
     * @return ImageInterface
     */
    public function setColor($index, $red, $green, $blue, $alpha = 0)
    {
        if (version_compare(PHP_VERSION, '5.4.0') >= 0) {
            imagecolorset($this->_resource, $index, $red, $green, $blue, $alpha);
        } else {
            imagecolorset($this->_resource, $index, $red, $green, $blue);
        }

        return $this;
    }

    /**
     * Fills the image with $color
     * Before filling, it allocates the color.
     * If no argument is provided, it fills with a transparent color.
     *
     * @see allocateColor()
     *
     * @param array|string|resource|ImageInterface $color
     * @param int                                  $positionX
     * @param int                                  $positionY
     *
     * @return ImageInterface
     */
    public function fill($color = 'f000', $positionX = 0, $positionY = 0)
    {
        if (static::isImageResource($color) or static::isImageBinary($color) or static::isImagePath($color)) {
            $color = new static($color);
        }

        if ($color instanceof ImageInterface) {
            imagesettile($this->_resource, $color->getResource());
            $color = IMG_COLOR_TILED;
        } else {
            $color = $this->allocateColor($color);
        }

        imagefill($this->_resource, $positionX, $positionY, $color);

        return $this;
    }

    /**
     * Sets the $color of a single pixel
     * The pixel's position is ($positionX, $positionY)
     *
     * @param string $color
     * @param int    $positionX
     * @param int    $positionY
     *
     * @return ImageInterface
     */
    public function pixel($color = 'f000', $positionX = 0, $positionY = 0)
    {
        imagesetpixel($this->_resource, $positionX, $positionY, $this->allocateColor($color));

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
     * Resource cloner
     *
     * @return resource
     */
    public function cloneResource()
    {
        if (isset($this->_resource)) {
            $w = $this->getWidth();
            $h = $this->getHeight();

            $clone = static::createBlank($w, $h);
            imagecopy($clone, $this->_resource, 0, 0, 0, 0, $w, $h);

            return $clone;
        } else {
            return null;
        }
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
     * Retrieves the effect engine
     *
     * @return EffectInterface
     */
    public function effect()
    {
        if (!isset($this->_effect)) {
            $this->_effect = new Effect($this);
        }

        return $this->_effect;
    }

    /**
     * Retrieves the draw engine
     *
     * @return DrawInterface
     */
    public function draw()
    {
        if (!isset($this->_draw)) {
            $this->_draw = new Draw($this);
        }

        return $this->_draw;
    }

    /**
     * Copies an $image into the current image
     * If $sourceWidth and $sourceHeight aren't specified, it takes $image dimensions
     *
     * @param ImageInterface|resource $image
     * @param int                     $destinationX
     * @param int                     $destinationY
     * @param int                     $sourceX
     * @param int                     $sourceY
     * @param int                     $destinationWidth
     * @param int                     $destinationHeight
     * @param int                     $sourceWidth
     * @param int                     $sourceHeight
     *
     * @throws \InvalidArgumentException
     * @return ImageInterface
     */
    public function copy($image, $destinationX, $destinationY, $sourceX, $sourceY, $destinationWidth, $destinationHeight, $sourceWidth = null, $sourceHeight = null)
    {
        if ($image instanceof ImageInterface) {
            $image = $image->getResource();
        } elseif (!static::isImageResource($image)) {
            throw new \InvalidArgumentException('The $resource provided is not a valid one: ' . var_dump($image));
        }

        imagecopyresampled($this->_resource, $image, $destinationX, $destinationY, $sourceX, $sourceY, $destinationWidth, $destinationHeight, $sourceWidth ?: imagesx($image), $sourceHeight ?: imagesy($image));

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
     * Formats a color into a string or array
     *
     * @param int|array $color
     * @param string    $format
     *
     * @return array|string
     * @throws \InvalidArgumentException
     */
    public static function formatColor($color, $format = 'array')
    {
        if (is_int($color)) {
            $color = array(
                'red'   => ($color >> 16) & 0xFF,
                'green' => ($color >> 8) & 0xFF,
                'blue'  => $color & 0xFF,
                'alpha' => ($color >> 24) & 0xFF,
            );
        }

        switch ($format) {
            case 'rgb':
                return sprintf('rgb(%d, %d, %d)', $color['red'], $color['green'], $color['blue']);
                break;

            case 'rgba':
                return sprintf('rgba(%d, %d, %d, %d)', $color['red'], $color['green'], $color['blue'], $color['alpha']);
                break;

            case 'hex':
                return sprintf('#%02x%02x%02x', $color['red'], $color['green'], $color['blue']);
                break;

            case 'hexa':
                return sprintf('#%02x%02x%02x%02x', static::parseAlpha($color['alpha'], true), $color['red'], $color['green'], $color['blue']);
                break;

            case 'int':
            case 'integer':
                return $color;
                break;

            case 'array':
                return array(
                    $color['red'],
                    $color['green'],
                    $color['blue'],
                    $color['alpha']
                );
                break;

            default:
                throw new \InvalidArgumentException('The given $format is invalid: ' . $format);
        }
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