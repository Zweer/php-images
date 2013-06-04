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
     * Initializes an empty image
     * If the $height is not specified, the image is squared.
     * If the $bgColor is not specified, the image is filled with a transparent
     * layer.
     *
     * @param int          $width   The width of the new empty image
     * @param int          $height  The height of the new empty image
     * @param array|string $bgColor The color to use for the background of the image
     */
    public function initEmpty(&$width, &$height = null, &$bgColor = null);

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
     * Color parser
     * Allocates the color in the image.
     *
     * @param array|string $color The color to parse
     *
     * @return null
     * @throws \InvalidArgumentException
     */
    public function parseColor($color);

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
     * Retrieves the manipulation engine
     *
     * @return ManipulateInterface
     */
    public function manipulate();

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
    public static function parseAlpha($alpha, $hex = false);
}