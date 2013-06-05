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
            case ImageWrapper::FLIP_VERTICAL:
                $y = $h - 1;
                $h *= -1;
                break;

            case ImageWrapper::FLIP_HORIZONTAL:
            default:
                $x = $w - 1;
                $w *= -1;
                break;
        }

        return $this->_modify(0, 0, $x, $y, $width, $height, $w, $h);
    }

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
    public function resize($width = null, $height = null, $ratio = true, $upsize = true)
    {
        // Parse the relative dimensions
        $this->_parseRelativeDimensions($width, $height);

        // Validates the passed parameters
        $width = !isset($width) ? null : intval($width);
        $height = $maxHeight = !isset($height) ? null : intval($height);
        $ratio = !!$ratio;
        $upsize = !!$upsize;

        $w = $this->_image->getWidth();
        $h = $this->_image->getHeight();

        if ($ratio) {
            /*
            |--------------------------------------------------------------------------
            | The ratio must be kept
            |--------------------------------------------------------------------------
            |
            | The ratio must be preserved, so we calculate width and height accordingly
            |
            */

            if (isset($width) and isset($height)) {
                /*
                |--------------------------------------------------------------------------
                | Both $width and $height are set
                |--------------------------------------------------------------------------
                |
                | In this case $width and $height assume the meaning of
                | $maxWidth and $maxHeight
                |
                */

                // Let's calculate the $height
                $height = intval($width / $w * $h);

                if ($height > $maxHeight) {
                    /*
                    |--------------------------------------------------------------------------
                    | The height is bigger of the $maxHeight
                    |--------------------------------------------------------------------------
                    |
                    | We set the $height to the $maxHeight and then calculate the
                    | width accordingly
                    |
                    */

                    $height = $maxHeight;
                    $width = intval($height / $h * $w);
                }
            } elseif (isset($width) or isset($height)) {
                /*
                |--------------------------------------------------------------------------
                | Only one of width and height is set
                |--------------------------------------------------------------------------
                |
                | We calculate the other variable accordingly
                |
                */

                $width = isset($width) ? $width : intval($height / $h * $w);
                $height = isset($height) ? $height : intval($width / $w * $h);
            }
        }

        if (!$upsize) {
            /*
            |--------------------------------------------------------------------------
            | The image can't be upsized
            |--------------------------------------------------------------------------
            |
            | We check if width and/or height are greater than the max.
            | In this case we resize the image accordingly.
            |
            */

            if (isset($width) and $width > $w) {
                /*
                |--------------------------------------------------------------------------
                | The given $width is greater than the image width
                |--------------------------------------------------------------------------
                |
                | We do not resize the image.
                |
                */

                $width = $w;

                if ($ratio) {
                    /*
                    |--------------------------------------------------------------------------
                    | The $ratio must be kept
                    |--------------------------------------------------------------------------
                    |
                    | We recalculate the height accordingly.
                    |
                    */

                    $height = intval($width / $w * $h);
                }
            }

            if (isset($height) and $height > $h) {
                /*
                |--------------------------------------------------------------------------
                | The given $height is greater than the image height
                |--------------------------------------------------------------------------
                |
                | We do not resize the image.
                |
                */

                $height = $h;

                if ($ratio) {
                    /*
                    |--------------------------------------------------------------------------
                    | The $ratio must be kept
                    |--------------------------------------------------------------------------
                    |
                    | We recalculate the width accordingly.
                    |
                    */

                    $width = intval($height / $h * $w);
                }
            }
        }

        if (!isset($width) and !isset($height)) {
            throw new \Exception('$width and $height must be set');
        } elseif (!isset($width)) {
            $width = $w;
        } elseif (!isset($height)) {
            $height = $h;
        }

        return $this->_modify(0, 0, 0, 0, $width, $height, $w, $h);
    }
}