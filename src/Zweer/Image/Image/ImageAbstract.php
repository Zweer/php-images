<?php

namespace Zweer\Image\Image;

use Zweer\Image\Manipulate\ManipulateInterface;
use Zweer\Image\Effect\EffectInterface;
use Zweer\Image\Draw\DrawInterface;
use Zweer\Image\Image;

abstract class ImageAbstract implements ImageInterface
{
    /**
     * @var resource
     */
    protected $_resource;

    /**
     * @var int
     */
    protected $_format;

    /**
     * @var string
     */
    protected $_filename;

    /**
     * @var ManipulateInterface
     */
    protected $_manipulate;

    /**
     * @var EffectInterface
     */
    protected $_effect;

    /**
     * @var DrawInterface
     */
    protected $_draw;

    /**
     * The constructor
     * If the filename is set does one of the following
     * (depending on the filename):
     * - initializes the image from a resource
     * - initializes the image from a binary string
     * - initializes the image from a file
     *
     * If the filename isn't set it initializes an empty image with the
     * specified width, height and background color.
     *
     * @param string|resource $filename The filename/resource/binary string representing the image
     * @param int             $width    The width of the new empty image
     * @param int             $height   The height of the new empty image, null for a squared image
     * @param array|string    $bgColor  The color to use for the background of the image
     */
    public function __construct($filename = null, $width = null, $height = null, $bgColor = null) {
        if (!isset($filename)) {
            $this->initEmpty($width, $height, $bgColor);
        } else {
            if (static::isImageResource($filename)) {
                $this->initFromResource($filename);
            } elseif (static::isImageBinary($filename)) {
                $this->initFromBinary($filename);
            } else {
                $this->initFromPath($filename);
            }
        }
    }

    /**
     * Returns the string representation of the image
     *
     * @return string
     */
    public function __toString()
    {
        return $this->encode();
    }

    /**
     * Clones the resource
     */
    public function __clone()
    {
        $this->_resource = $this->cloneResource();
    }

    /**
     * Initializes the image from a resource
     *
     * @param resource $resource
     *
     * @throws \InvalidArgumentException
     */
    public function initFromResource($resource)
    {
        if (!static::isImageResource($resource)) {
            throw new \InvalidArgumentException('The $resource provided is not a valid one: ' . var_dump($resource));
        }

        $this->_resource = $resource;
    }

    /**
     * Initializes the image from a binary string
     * It only checks if the argument is a valid binary string.
     *
     * @abstract
     *
     * @param string $binary
     *
     * @throws \InvalidArgumentException
     */
    public function initFromBinary($binary)
    {
        if (!static::isImageBinary($binary)) {
            throw new \InvalidArgumentException('The $binary provided is not a valid one: ' . var_dump($binary));
        }
    }

    /**
     * Initializes the image from a path
     * It only checks if the argument is a valid filename.
     *
     * @abstract
     *
     * @param string $filename
     *
     * @throws \InvalidArgumentException
     */
    public function initFromPath($filename)
    {
        if (!static::isImagePath($filename)) {
            throw new \InvalidArgumentException('The $filename provided is not a valid one: ' . var_dump($filename));
        }
    }

    /**
     * Saves the current image
     * It only checks if the filename is set.
     *
     * @abstract
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
        if (!isset($filename) and !isset($this->_filename)) {
            throw new \Exception('To save a file you must provide a valid $filename');
        }
    }

    /**
     * Encodes the image as a base64 string
     *
     * @param int $format
     * @param int $quality
     *
     * @return string
     */
    public function encode($format = null, $quality = null)
    {
        ob_start();

        $mime = $this->output($format, $quality, false);

        $data = ob_get_contents();
        ob_end_clean();

        return sprintf('data:%s;base64,%s', $mime, base64_encode($data));
    }

    /**
     * Retrieves the orientation of the image
     *
     * @return string
     */
    public function getOrientation()
    {
        if ($this->getWidth() > $this->getHeight()) {
            return Image::ORIENTATION_LANDSCAPE;
        } elseif ($this->getWidth() < $this->getHeight()) {
            return Image::ORIENTATION_PORTRAIT;
        } else {
            return Image::ORIENTATION_SQUARE;
        }
    }

    /**
     * Resource getter
     *
     * @return resource
     */
    public function getResource()
    {
        return $this->_resource;
    }

    /**
     * Resource setter
     *
     * @param resource $resource
     *
     * @throws \InvalidArgumentException
     * @return ImageInterface
     */
    public function setResource($resource)
    {
        if (!static::isImageResource($resource)) {
            throw new \InvalidArgumentException('The $resource provided is not a valid one: ' . var_dump($resource));
        }

        $this->_resource = $resource;

        return $this;
    }

    /**
     * Inserts an image into the current image
     * It's a wrapper of copy() using the $anchor
     *
     * @param ImageInterface|resource $image
     * @param int                     $positionX
     * @param int                     $positionY
     * @param string                  $anchor
     *
     * @throws \InvalidArgumentException
     * @return ImageInterface
     */
    public function insert($image, $positionX = 0, $positionY = 0, $anchor = 'top left')
    {
        if (static::isImageResource($image)) {
            $image = new static($image);
        } elseif (!$image instanceof ImageInterface) {
            throw new \InvalidArgumentException('The $resource provided is not a valid one: ' . var_dump($image));
        }

        switch ($anchor) {
            case 'top':
            case 'top center':
            case 'top middle':
            case 'center top':
            case 'middle top':
                $positionX = intval((($this->getWidth() - $image->getWidth()) / 2) + $positionX);
                $positionY = intval($positionY);
                break;

            case 'top right':
            case 'right top':
                $positionX = intval($this->getWidth() - $image->getWidth() - $positionX);
                $positionY = intval($positionY);
                break;

            case 'left':
            case 'left center':
            case 'left middle':
            case 'center left':
            case 'middle left':
                $positionX = intval($positionX);
                $positionY = intval((($this->getHeight() - $image->getHeight()) / 2) + $positionY);
                break;

            case 'right':
            case 'right center':
            case 'right middle':
            case 'center right':
            case 'middle right':
                $positionX = intval($this->getWidth() - $image->getWidth() - $positionX);
                $positionY = intval((($this->getHeight() - $image->getHeight()) / 2) + $positionY);
                break;

            case 'bottom left':
            case 'left bottom':
                $positionX = intval($positionX);
                $positionY = intval($this->getHeight() - $image->getHeight() - $positionY);
                break;

            case 'bottom':
            case 'bottom center':
            case 'bottom middle':
            case 'center bottom':
            case 'middle bottom':
                $positionX = intval((($this->getWidth() - $image->getWidth()) / 2) + $positionX);
                $positionY = intval($this->getHeight() - $image->getHeight() - $positionY);
                break;

            case 'bottom right':
            case 'right bottom':
                $positionX = intval($this->getWidth() - $image->getWidth() - $positionX);
                $positionY = intval($this->getHeight() - $image->getHeight() - $positionY);
                break;

            case 'center':
            case 'middle':
            case 'center center':
            case 'middle middle':
                $positionX = intval((($this->getWidth() - $image->getWidth()) / 2) + $positionX);
                $positionY = intval((($this->getHeight() - $image->getHeight()) / 2) + $positionY);
                break;

            default:
            case 'top left':
            case 'left top':
                $positionX = intval($positionX);
                $positionY = intval($positionY);
                break;
        }

        return $this->copy($image, $positionX, $positionY, 0, 0, $image->getWidth(), $image->getHeight());
    }

    /**
     * Apply given image to the current image as an alpha mask
     *
     * @param ImageInterface|resource $image
     * @param bool                    $maskWidthAlpha
     *
     * @return ImageInterface
     */
    public function mask($image, $maskWidthAlpha = false)
    {
        $maskedImage = new static(null, $this->getWidth(), $this->getHeight());
        $mask = $image instanceof ImageInterface ? $image : new static($image);

        $w = $this->getWidth();
        $h = $this->getHeight();

        if ($mask->getWidth() != $w or $mask->getHeight() != $h) {
            $mask->manipulate()->resize($w, $h);
        }

        for ($x = 0; $x < $w; ++$x) {
            for ($y = 0; $y < $h; ++$y) {
                $color = $this->pickColor($x, $y);
                $alpha = $mask->pickColor($x, $y);

                if ($maskWidthAlpha) {
                    $alpha = $alpha[3];
                } else {
                    // Use red channel as mask
                    $alpha = floatval(round($alpha[0] / 255, 2));
                }

                if ($color[3] < $alpha) {
                    // Preserve original alpha
                    $alpha = $color[3];
                }

                $maskedImage->pixel(array($color[0], $color[1], $color[2], $alpha), $x, $y);
            }
        }

        $this->_resource = $maskedImage->getResource();

        return $this;
    }

    /**
     * Copies the current image into $image and than replace it
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
     * @throws \InvalidArgumentException
     * @return ImageInterface
     */
    public function replace(ImageInterface $image, $destinationX, $destinationY, $sourceX, $sourceY, $destinationWidth, $destinationHeight, $sourceWidth, $sourceHeight)
    {
        $image->copy($this, $destinationX, $destinationY, $sourceX, $sourceY, $destinationWidth, $destinationHeight, $sourceWidth, $sourceHeight);

        return $this->setResource($image->cloneResource());
    }

    /**
     * The parameter is an image identifier?
     * States if the argument is an image resource.
     * It is usually extended to know if the resource is of the right library.
     *
     * @param $resource
     *
     * @return bool
     */
    public static function isImageResource($resource)
    {
        return is_resource($resource);
    }

    /**
     * The parameter is an image binary string?
     * States if the argument is an image binary string.
     * A binary string is characterized by characters that do not translate
     * themselves into printable entities.
     *
     * @param $string
     *
     * @return bool
     */
    public static function isImageBinary($string)
    {
        return !ctype_print($string);
    }

    /**
     * The parameter is a file?
     * States if the argument represents an image from it's path.
     * The path can be both local and an URL.
     *
     * @todo The path must be a valid image file
     *
     * @param $filename
     *
     * @return bool
     */
    public static function isImagePath($filename)
    {
        return is_file($filename);
    }

    /**
     * Parses the alpha
     * Parses the alpha and converts it into a valid value for the libraries.
     * Normally it converts [0-1] into [0-127].
     * With $hex it converts [1-255] into [0-127].
     *
     * @param int  $alpha
     * @param bool $hex
     *
     * @return int
     */
    public static function parseAlpha($alpha, $hex = false)
    {
        if ($hex) {
            $alpha += 0;
            $range_input = range(0, 255, 255/127);
            $range_output = range(127, 0);
        } else {
            $range_input = range(1, 0, 1/127);
            $range_output = range(0, 127);
        }

        foreach ($range_input as $key => $value) {
            if ($value <= $alpha) {
                return $range_output[$key];
            }
        }

        return 127;
    }

    /**
     * Color parser
     * Parses the color and returns and array with all the components:
     * [0]: red,
     * [1]: green,
     * [2]: blue,
     * [3]: alpha
     * All the values are between [0-255] except alpha that is library dependant
     *
     * @param array|string $color The color to parse
     *
     * @return array 0: red, 1: green, 2: blue, 3: alpha
     * @throws \InvalidArgumentException
     */
    public static function parseColor($color)
    {
        $alpha = 0;
        $red = null;
        $green = null;
        $blue = null;

        if (is_array($color)) {
            /*
            |--------------------------------------------------------------------------
            | The color is given in the form of an array
            |--------------------------------------------------------------------------
            |
            | The array is made of these elements:
            | - red:    [0-255]
            | - green:  [0-255]
            | - blue:   [0-255]
            | - alpha?: [0-1]
            |
            */

            if (count($color) < 3 or count($color) > 4) {
                /*
                |--------------------------------------------------------------------------
                | The array provided is not valid
                |--------------------------------------------------------------------------
                |
                | The array provided as too many or too few elements
                |
                */

                throw new \InvalidArgumentException('The $color array must be of 3 or 4 elements: ' . var_dump($color));
            }

            if (count($color) == 4) {
                /*
                |--------------------------------------------------------------------------
                | The array is provided with the alpha channel
                |--------------------------------------------------------------------------
                |
                | The alpha channel is retrieved and converted in the right format
                |
                */

                $alpha = static::parseAlpha(array_pop($color));
            }

            /*
            |--------------------------------------------------------------------------
            | The colors are retrieved
            |--------------------------------------------------------------------------
            |
            | The main colors are retrieved and stored
            |
            */

            list($red, $green, $blue) = $color;
        } elseif (is_string($color)) {
            $hexRegex = '[a-f0-9]';
            $decRegex = '[0-9]';

            if (preg_match('/^#?(?P<alpha>' . $hexRegex . '{1})?(?P<red>' . $hexRegex . '{1})(?P<green>' . $hexRegex . '{1})(?P<blue>' . $hexRegex . '{1})$/i', $color, $matches)) {
                /*
                |--------------------------------------------------------------------------
                | The color is provided as a hexadecimal short string
                |--------------------------------------------------------------------------
                |
                | Possible formats are:
                | - #999
                | - 999
                |
                | You could also append the alpha value:
                | - #999F
                | - 999F
                |
                */

                return static::parseColor($matches['alpha'] . $matches['alpha'] . $matches['red'] . $matches['red'] . $matches['green'] . $matches['green'] . $matches['blue'] . $matches['blue']);
            } elseif (preg_match('/^#?(?P<alpha>' . $hexRegex . '{2})?(?P<red>' . $hexRegex . '{2})(?P<green>' . $hexRegex . '{2})(?P<blue>' . $hexRegex . '{2})$/i', $color, $matches)) {
                /*
                |--------------------------------------------------------------------------
                | The color is provided as a hexadecimal string
                |--------------------------------------------------------------------------
                |
                | Possible formats are:
                | - #999999
                | - 999999
                |
                | You could also append the alpha value:
                | - #FF999999
                | - FF999999
                |
                */

                $red = '0x' . $matches['red'];
                $green = '0x' . $matches['green'];
                $blue = '0x' . $matches['blue'];
                $alpha = static::parseAlpha('0x' . $matches['alpha'], true);
            } elseif (preg_match('/rgba? ?\((?P<red>' . $decRegex . '{1,3}), ?(?P<green>' . $decRegex . '{1,3}), ?(?P<blue>' . $decRegex . '{1,3}),? ?(?P<alpha>[0-9.]{1,4})?\)/i', $color, $matches)) {
                /*
                |--------------------------------------------------------------------------
                | The color is provided as a rgb[a] function
                |--------------------------------------------------------------------------
                |
                | Possible formats are:
                | - rgb(red, green, blue)
                | - rgba(red, green, blue, alpha)
                |
                | 'red', 'green' and 'blue' must be integers between 0 and 255
                | 'alpha' must be a float between 0 and 1, 3 decimal digits allowed
                |
                | It's allowed to:
                | - use the rgba expression even without the alpha channel and viceversa
                | - use one space after the rgb[a] string, before the '('
                | - use one space after each coma
                |
                */

                $red = $matches['red'];
                $green = $matches['green'];
                $blue = $matches['blue'];
                $alpha = static::parseAlpha($matches['alpha']);
            }
        }

        if (!isset($red) or !isset($green) or !isset($blue)) {
            throw new \InvalidArgumentException('The $color provided is not a valid one: ' . var_dump($color));
        }

        return array($red, $green, $blue, $alpha);
    }

    /**
     * Parses the dimensions into valid values
     * It returns an array with width and height.
     *
     * @param int $width
     * @param int $height
     *
     * @return array 0: width, 1: height
     */
    public static function parseDimensions($width, $height = null)
    {
        $width = is_numeric($width) ? intval($width) : 1;
        $height = !isset($height) ? $width : (is_numeric($height) ? intval($height) : 1);

        return array($width, $height);
    }
}