<?php

namespace Zweer\Image\Driver;

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
        if (is_null($filename)) {
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
     * Initializes an empty image
     * If the $height is not specified, the image is squared.
     *
     * @abstract
     *
     * @param int          $width   The width of the new empty image
     * @param int          $height  The height of the new empty image
     * @param array|string $bgColor The color to use for the background of the image
     */
    public function initEmpty(&$width, &$height = null, &$bgColor = null)
    {
        $width = is_numeric($width) ? intval($width) : 1;
        $height = is_null($height) ? $width : (is_numeric($height) ? intval($height) : 1);
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
     * Color parser
     * This method does nothing but parsing the rgba integers to facilitate
     * what the library-dependant function will do.
     *
     * @abstract
     *
     * @param array|string $color The color to parse
     * @param null         $red   Only used for internal parsing
     * @param null         $green Only used for internal parsing
     * @param null         $blue  Only used for internal parsing
     * @param int          $alpha Only used for internal parsing
     *
     * @return null
     * @throws \InvalidArgumentException
     */
    public function parseColor($color, &$red = null, &$green = null, &$blue = null, &$alpha = 0)
    {
        $alpha = 0;

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

            if (count($color) < 3 || count($color) > 4) {
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

        if (is_null($red) || is_null($green) || is_null($blue)) {
            throw new \InvalidArgumentException('The $color provided is not a valid one: ' . var_dump($color));
        }

        return null;
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
}