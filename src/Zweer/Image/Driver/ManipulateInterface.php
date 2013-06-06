<?php

namespace Zweer\Image\Driver;

interface ManipulateInterface extends EngineInterface
{
    /**
     * Returns the string representation of the image
     *
     * @return string
     */
    public function __toString();

    /**
     * Flips the image horizontally (default) or vertically
     *
     * @param string $mode
     *
     * @return ManipulateInterface
     */
    public function flip($mode = null);

    /**
     * Resizes the image
     * You can decide if preserving the ratio and upsizing the image.
     * $width and $height can be specified relative to the actual image size:
     * - '+2' is 2 pixel more than the actual size;
     * - '-2' (both an int or a string) is 2 pixel less than the actual size
     * - '2%' is the percentage of the actua size
     *
     * @param int|string  $width
     * @param int|string  $height
     * @param bool $ratio
     * @param bool $upsize
     *
     * @return ManipulateInterface
     * @throws \Exception
     */
    public function resize($width = null, $height = null, $ratio = true, $upsize = true);

    /**
     * Resize image canvas
     *
     * @see _modify
     *
     * @param int    $width
     * @param int    $height
     * @param string $anchor
     * @param string $bgColor
     *
     * @return ManipulateInterface
     */
    public function canvas($width = null, $height = null, $anchor = 'center', $bgColor = null);

    /**
     * Crops an image of $width x $height, starting from ($positionX, $positionY)
     * If the $height is null, the crop area is squared
     *
     * @param int $width
     * @param int $height
     * @param int $positionX
     * @param int $positionY
     *
     * @throws \Exception
     * @return ManipulateInterface
     */
    public function crop($width, $height = null, $positionX = null, $positionY = null);

    /**
     * Cut out a detail of the image in given ratio and resize to output size
     * If the $height is null, the area to grab is squared.
     *
     * @param int $width
     * @param int $height
     *
     * @return ManipulateInterface
     */
    public function grab($width, $height = null);
}