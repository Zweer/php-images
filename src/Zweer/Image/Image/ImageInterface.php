<?php

namespace Zweer\Image\Image;

use Zweer\Image\Manipulate\ManipulateInterface;
use Zweer\Image\Effect\EffectInterface;
use Zweer\Image\Draw\DrawInterface;

interface ImageInterface
{
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
    public function __construct($filename = null, $width = null, $height = null, $bgColor = null);

    /**
     * Destroys the current image resource, freeing space
     */
    public function __destruct();

    /**
     * Clones the resource
     */
    public function __clone();

    /**
     * Returns the string representation of the image
     *
     * @return string
     */
    public function __toString();

    /**
     * Initializes an empty image
     * If the $height is not specified, the image is squared.
     * If the $bgColor is not specified, the image is filled with a transparent
     * layer.
     *
     * @param int          $width   The width of the new empty image
     * @param int          $height  The height of the new empty image
     * @param array|string $bgColor The color to use for the background of the image
     */
    public function initEmpty($width, $height = null, $bgColor = null);

    /**
     * Initializes the image from a resource
     *
     * @param resource $resource
     *
     * @throws \InvalidArgumentException
     */
    public function initFromResource($resource);

    /**
     * Initializes the image from a binary string
     *
     * @param string $binary
     *
     * @throws \InvalidArgumentException
     */
    public function initFromBinary($binary);

    /**
     * Initializes the image from a path
     *
     * @param string $filename
     *
     * @throws \InvalidArgumentException
     */
    public function initFromPath($filename);

    /**
     * Saves the current image
     * If the filename is not specified it takes the original filename (if one).
     * With PNGs and JPEGs the quality attribute states the image quality.
     *
     * @param string $filename
     * @param int    $quality
     *
     * @return ImageInterface
     * @throws \InvalidArgumentException
     * @throws \Exception
     */
    public function save($filename = null, $quality = null);

    /**
     * Outputs the image to the stdout
     *
     * @param int  $format
     * @param int  $quality
     * @param bool $header
     *
     * @return int The format of the outputted image
     * @throws \InvalidArgumentException
     * @throws \Exception
     */
    public function output($format = null, $quality = null, $header = true);

    /**
     * Encodes the image as a base64 string
     *
     * @param int $format
     * @param int $quality
     *
     * @return string
     */
    public function encode($format = null, $quality = null);

    /**
     * Pick the color at ($x, $y)
     *
     * @param int    $x
     * @param int    $y
     * @param string $format
     *
     * @return string|int|array
     */
    public function pickColor($x, $y, $format = 'array');

    /**
     * Retrieves all the colors of the image
     * If $format is null, it returns the integer representation
     *
     * @param string $format
     *
     * @return string[]|int[]|array[]
     */
    public function pickColors($format = null);

    /**
     * Allocates the $color in the current image
     *
     * @see parseColor()
     *
     * @param array|string $color
     *
     * @return int The color identifier
     */
    public function allocateColor($color);

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
    public function setColor($index, $red, $green, $blue, $alpha = 0);

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
    public function fill($color = 'f000', $positionX = 0, $positionY = 0);

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
    public function pixel($color = 'f000', $positionX = 0, $positionY = 0);

    /**
     * Retrieves the width of the image
     *
     * @return int
     */
    public function getWidth();

    /**
     * Retrieves the height of the image
     *
     * @return int
     */
    public function getHeight();

    /**
     * Retrieves the orientation of the image
     *
     * @return string
     */
    public function getOrientation();

    /**
     * Resource getter
     *
     * @return resource
     */
    public function getResource();

    /**
     * Resource cloner
     *
     * @return resource
     */
    public function cloneResource();

    /**
     * Resource setter
     *
     * @param resource $resource
     *
     * @throws \InvalidArgumentException
     * @return ImageInterface
     */
    public function setResource($resource);

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
    public function copy($image, $destinationX, $destinationY, $sourceX, $sourceY, $destinationWidth, $destinationHeight, $sourceWidth = null, $sourceHeight = null);

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
    public function insert($image, $positionX = 0, $positionY = 0, $anchor = 'top left');

    /**
     * Apply given image to the current image as an alpha mask
     *
     * @param ImageInterface|resource $image
     * @param bool                    $maskWidthAlpha
     *
     * @return ImageInterface
     */
    public function mask($image, $maskWidthAlpha = false);

    /**
     * Sets the opacity of the image
     * $transparency should be between 0 and 100
     *
     * @param int $transparency
     *
     * @return ImageInterface
     */
    public function opacity($transparency);

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
    public function replace(ImageInterface $image, $destinationX, $destinationY, $sourceX, $sourceY, $destinationWidth, $destinationHeight, $sourceWidth, $sourceHeight);

    /**
     * Retrieves the manipulation engine
     *
     * @return ManipulateInterface
     */
    public function manipulate();

    /**
     * Retrieves the effect engine
     *
     * @return EffectInterface
     */
    public function effect();

    /**
     * Retrieves the draw engine
     *
     * @return DrawInterface
     */
    public function draw();

    /**
     * Parses the alpha
     * Parses the alpha and converts it into a valid value for the libraries.
     * Normally it converts [0-1] into [0-127].
     * With $hex it converts [0-255] into [0-127].
     *
     * @param int  $alpha
     * @param bool $hex
     *
     * @return int
     */
    public static function parseAlpha($alpha, $hex = false);

    /**
     * Color parser
     * Parses the color and returns and array with all the components:
     * [0]: red,
     * [1]: green,
     * [2]: blue,
     * [3]: alpha
     * All the values are between [0-255] except alpha that is library dependant
     *
     * @see parseAlpha()
     *
     * @param array|string $color The color to parse
     *
     * @return array 0: red, 1: green, 2: blue, 3: alpha
     * @throws \InvalidArgumentException
     */
    public static function parseColor($color);

    /**
     * Formats a color into a string or array
     *
     * @param int|array $color
     * @param string    $format
     *
     * @return array|string
     * @throws \InvalidArgumentException
     */
    public static function formatColor($color, $format = 'array');

    /**
     * Creates a blank image
     *
     * @param int $width
     * @param int $height
     *
     * @return resource
     */
    public static function createBlank($width, $height = null);
}