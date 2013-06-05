<?php

namespace Zweer\Image\Driver\Gd;

use Zweer\Image\Driver\ManipulateAbstract;
use Zweer\Image\Driver\ManipulateInterface;
use Zweer\Image\Image as ImageWrapper;

class Manipulate extends ManipulateAbstract
{
    /**
     * Modify wrapper
     * Used in many function such as resize and grab
     *
     * @param int  $destinationX      Destination X coord
     * @param int  $destinationY      Destination Y coord
     * @param int  $sourceX           Source X coord
     * @param int  $sourceY           Source Y coord
     * @param int  $destinationWidth  Destination width
     * @param int  $destinationHeight Destination height
     * @param int  $sourceWidth       Source width
     * @param int  $sourceHeight      Source height
     * @param null $bgColor           The color to use as the background
     *
     * @return ManipulateInterface
     */
    protected function _modify($destinationX, $destinationY, $sourceX, $sourceY, $destinationWidth, $destinationHeight, $sourceWidth, $sourceHeight, $bgColor = null)
    {
        $newImage = ImageWrapper::create($destinationWidth, $destinationHeight, $bgColor);

        $this->_image->replace($newImage, $destinationX, $destinationY, $sourceX, $sourceY, $destinationWidth, $destinationHeight, $sourceWidth, $sourceHeight);

        return $this;
    }
}