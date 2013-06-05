<?php

namespace Zweer\Image\Driver;

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
    public function fill($color = 'f000');

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
    public function copy(ImageInterface $image, $destinationX, $destinationY, $sourceX, $sourceY, $destinationWidth, $destinationHeight, $sourceWidth, $sourceHeight);

    /**
     * Retrieves the manipulation engine
     *
     * @return ManipulateInterface
     */
    public function manipulate();

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
     * Creates a blank image
     *
     * @param int $width
     * @param int $height
     *
     * @return resource
     */
    public static function createBlank($width, $height = null);
}