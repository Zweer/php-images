<?php

namespace Zweer\Image\Driver\Gd;

use Zweer\Image\Driver\ManipulateAbstract;
use Zweer\Image\Driver\ManipulateInterface;
use Zweer\Image\Image;

class Manipulate extends ManipulateAbstract
{
    /**
     * Modify wrapper
     * Used in many function such as resize and grab
     *
     * @param int $dst_x Destination X coord
     * @param int $dst_y Destination Y coord
     * @param int $src_x Source X coord
     * @param int $src_y Source Y coord
     * @param int $dst_w Destination width
     * @param int $dst_h Destination height
     * @param int $src_w Source width
     * @param int $src_h Source height
     *
     * @return ManipulateInterface
     */
    protected function _modify($dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h)
    {
        // create new image
        $image = imagecreatetruecolor($dst_w, $dst_h);

        // preserve transparency
        imagealphablending($image, false);
        imagesavealpha($image, true);

        // copy content from resource
        imagecopyresampled($image, $this->_image->getResource(), $dst_x , $dst_y , $src_x , $src_y , $dst_w , $dst_h , $src_w , $src_h);

        // set new content as recource
        $this->_image->setResource($image);

        return $this;
    }

    /**
     * Flips the image horizontally (default) or vertically
     *
     * @param string $mode
     *
     * @return ManipulateInterface
     */
    public function flip($mode = null)
    {
        $x = $y = 0;
        $w = $width = $this->_image->getWidth();
        $h = $height = $this->_image->getHeight();

        switch (strtolower($mode)) {
            case Image::FLIP_VERTICAL:
                $y = $h - 1;
                $h *= -1;
                break;

            case Image::FLIP_HORIZONTAL:
            default:
                $x = $w - 1;
                $w *= -1;
                break;
        }

        return $this->_modify(0, 0, $x, $y, $width, $height, $w, $h);
    }
}