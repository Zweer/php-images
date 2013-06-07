<?php

namespace Zweer\Image\Manipulate;

use Zweer\Image\Engine\EngineAbstract;
use Zweer\Image\Image as ImageWrapper;

abstract class ManipulateAbstract extends EngineAbstract implements ManipulateInterface
{
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
    abstract protected function _modify($destinationX, $destinationY, $sourceX, $sourceY, $destinationWidth, $destinationHeight, $sourceWidth, $sourceHeight, $bgColor = null, $sourceWidthAtDestination = null, $sourceHeightAtDestination = null);

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
    public function canvas($width = null, $height = null, $anchor = 'center', $bgColor = null)
    {
        // Parse the relative dimensions
        $this->_parseRelativeDimensions($width, $height);

        // Validates the passed parameters
        $w = $this->_image->getWidth();
        $h = $this->_image->getHeight();
        $width = !isset($width) ? $w : intval($width);
        $height = !isset($height) ? $h : intval($height);

        if ($width > $w) {
            $sourceWidth = $w;
        } else {
            $sourceWidth = $width;
        }

        if ($height > $h) {
            $sourceHeight = $h;
        } else {
            $sourceHeight = $height;
        }

        switch ($anchor) {
            case 'top left':
            case 'left top':
                $sourceX = 0;
                $sourceY = 0;

                $destinationX = 0;
                $destinationY = 0;
                break;

            case 'top':
            case 'top center':
            case 'center top':
            case 'top middle':
            case 'middle top':
                $sourceX = $width < $w ? intval(($w - $width) / 2) : 0;
                $sourceY = 0;

                $destinationX = $width > $w ? intval(($width - $w) / 2) : 0;
                $destinationY = 0;
                break;

            case 'top right':
            case 'right top':
                $sourceX = $width < $w ? intval($w - $width) : 0;
                $sourceY = 0;

                $destinationX = $width > $w ? intval($width - $w) : 0;
                $destinationY = 0;
                break;

            case 'left':
            case 'left center':
            case 'left middle':
            case 'center left':
            case 'middle left':
                $sourceX = 0;
                $sourceY = $height < $h ? intval(($h - $height) / 2) : 0;

                $destinationX = 0;
                $destinationY = $height > $h ? intval(($height - $h) / 2) : 0;
                break;

            default:
            case 'center':
            case 'middle':
            case 'center center':
            case 'middle middle':
                $sourceX = $width < $w ? intval(($w - $width) / 2) : 0;
                $sourceY = $height < $h ? intval(($h - $height) / 2) : 0;

                $destinationX = $width > $w ? intval(($width - $w) / 2) : 0;
                $destinationY = $height > $h ? intval(($height - $h) / 2) : 0;
                break;

            case 'right':
            case 'right center':
            case 'right middle':
            case 'center right':
            case 'middle right':
                $sourceX = $width < $w ? intval($w - $width) : 0;
                $sourceY = $height < $h ? intval(($h - $height) / 2) : 0;


                $destinationX = $width > $w ? intval($width - $w) : 0;
                $destinationY = $height > $h ? intval(($height - $h) / 2) : 0;
                break;

            case 'bottom left':
            case 'left bottom':
                $sourceX = 0;
                $sourceY = $height < $h ? intval($h - $height) : 0;

                $destinationX = 0;
                $destinationY = $height - $h;
                break;

            case 'bottom':
            case 'bottom center':
            case 'bottom middle':
            case 'center bottom':
            case 'middle bottom':
                $sourceX = $width < $w ? intval(($w - $width) / 2) : 0;
                $sourceY = $height < $h ? intval($h - $height) : 0;

                $destinationX = $width > $w ? intval(($width - $w) / 2) : 0;
                $destinationY = $height - $h;
                break;

            case 'bottom right':
            case 'right bottom':
                $sourceX = $width < $w ? intval($w - $width) : 0;
                $sourceY = $height < $h ? intval($h - $height) : 0;

                $destinationX = $width > $w ? intval($width - $w) : 0;
                $destinationY = $height - $h;
                break;
        }

        var_dump(array(
            'dstX'=> $destinationX,
            'dstY' => $destinationY,
            'srcX' => $sourceX,
            'srcY' => $sourceY,
            'dstW' => $width,
            'dstH' => $height,
            'srcW' => $sourceWidth,
            'srcH' => $sourceHeight,
            'bgColor' => $bgColor));

        return $this->_modify($destinationX, $destinationY, $sourceX, $sourceY, $width, $height, $sourceWidth, $sourceHeight, $bgColor, $sourceWidth, $sourceHeight);
    }

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
    public function crop($width, $height = null, $positionX = null, $positionY = null)
    {
        // Validates the current arguments
        $width = is_numeric($width) ? intval($width) : null;
        $height = is_numeric($height) ? intval($height) : $width;
        $positionX = is_numeric($positionX) ? intval($positionX) : null;
        $positionY = is_numeric($positionY) ? intval($positionY) : null;

        if (is_null($positionX) && is_null($positionY)) {
            // center position of width/height rectangle
            $positionX = floor(($this->_image->getWidth() - intval($width)) / 2);
            $positionY = floor(($this->_image->getHeight() - intval($height)) / 2);
        }

        if (is_null($width) || is_null($height)) {
            throw new \InvalidArgumentException('The crop area must have $width and $height defined');
        }

        return $this->_modify(0, 0, $positionX , $positionY, $width, $height, $width, $height);
    }

    /**
     * Cut out a detail of the image in given ratio and resize to output size
     * If the $height is null, the area to grab is squared.
     *
     * @param int $width
     * @param int $height
     *
     * @return ManipulateInterface
     */
    public function grab($width, $height = null)
    {
        // Validates the current arguments
        $width = is_numeric($width) ? intval($width) : null;
        $height = is_numeric($height) ? intval($height) : $width;

        $grabWidth = $w = $this->_image->getWidth();
        $h = $this->_image->getHeight();
        $ratio = $grabWidth / $width;

        if ($height * $ratio <= $h) {
            $grabHeight = round($height * $ratio);
            $sourceX = 0;
            $sourceY = round(($h - $grabHeight) / 2);
        } else {
            $grabHeight = $h;
            $ratio = $grabHeight / $height;
            $grabWidth = round($width * $ratio);
            $sourceX = round(($w - $grabWidth) / 2);
            $sourceY = 0;
        }

        return $this->_modify(0, 0, $sourceX, $sourceY, $width, $height, $grabWidth, $grabHeight);
    }
}