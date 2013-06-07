<?php

namespace Zweer\Image\Driver\Gd;

use Zweer\Image\Manipulate\ManipulateAbstract;
use Zweer\Image\Manipulate\ManipulateInterface;
use Zweer\Image\Image as ImageWrapper;

class Manipulate extends ManipulateAbstract
{
    /**
     * Modify wrapper
     * Used in many function such as resize and grab
     *
     * @param int          $destinationX              Destination X coord
     * @param int          $destinationY              Destination Y coord
     * @param int          $sourceX                   Source X coord
     * @param int          $sourceY                   Source Y coord
     * @param int          $destinationWidth          Destination width
     * @param int          $destinationHeight         Destination height
     * @param int          $sourceWidth               Source width
     * @param int          $sourceHeight              Source height
     * @param string|array $bgColor                   The color to use as the background
     * @param int          $sourceWidthAtDestination
     * @param int          $sourceHeightAtDestination
     *
     * @return ManipulateInterface
     */
    protected function _modify($destinationX, $destinationY, $sourceX, $sourceY, $destinationWidth, $destinationHeight, $sourceWidth, $sourceHeight, $bgColor = null, $sourceWidthAtDestination = null, $sourceHeightAtDestination = null)
    {
        $newImage = ImageWrapper::create($destinationWidth, $destinationHeight, $bgColor);

        $this->_image->replace($newImage,
            $destinationX, $destinationY,
            $sourceX, $sourceY,
            isset($sourceWidthAtDestination) ? $sourceWidthAtDestination : $destinationWidth, isset($sourceHeightAtDestination) ? $sourceHeightAtDestination : $destinationHeight,
            $sourceWidth, $sourceHeight
        );

        return $this;
    }

    /**
     * Rotates the image of $angle degrees
     *
     * @param int          $angle
     * @param string|array $bgColor
     * @param bool         $ignoreTransparent
     *
     * @return ManipulateInterface
     */
    public function rotate($angle, $bgColor = 'ffff', $ignoreTransparent = false)
    {
        $this->_image->setResource(imagerotate($this->_image->getResource(), $angle, $this->_image->allocateColor($bgColor), $ignoreTransparent ? 1 : 0));

        return $this;
    }
}