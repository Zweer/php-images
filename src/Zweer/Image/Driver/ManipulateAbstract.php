<?php

namespace Zweer\Image\Driver;

abstract class ManipulateAbstract implements ManipulateInterface
{
    /**
     * @var ImageInterface
     */
    protected $_image;

    /**
     * The contructor
     * It only stores the $image argument.
     *
     * @param ImageInterface $image
     */
    public function __construct(ImageInterface $image)
    {
        $this->_image = $image;
    }

    /**
     * Helper for the resizeing methods
     * Parses the $width and $height if it's set to a relative value
     * $width and $height can be specified relative to the actual image size:
     * - '+2' is 2 pixel more than the actual size;
     * - '-2' (both an int or a string) is 2 pixel less than the actual size
     * - '2%' is the percentage of the actua size
     *
     * @see resize()
     *
     * @param int|string $width
     * @param int|string $height
     */
    protected function _parseRelativeDimensions(&$width = null, &$height = null)
    {
        $w = $this->_image->getWidth();
        $h = $this->_image->getHeight();

        if (strpos($width, '+') !== false or strpos($width, '-') !== false) {
            $width += $w;
        }

        if (strpos($height, '+') !== false or strpos($height, '-') !== false) {
            $height += $h;
        }

        if (strpos($width, '%') !== false) {
            $width = intval($w / 100 * $width);
        }

        if (strpos($height, '%') !== false) {
            $height = intval($h / 100 * $height);
        }
    }
}