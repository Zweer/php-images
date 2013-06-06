<?php

namespace Zweer\Image\Driver\Gd;

use Zweer\Image\Draw\DrawAbstract;
use Zweer\Image\Draw\DrawInterface;

class Draw extends DrawAbstract
{
    /**
     * Draws a rectangle
     * It starts at ($x1, $y1) and ends at ($x2, $y2)
     *
     * @param string|array $color
     * @param int          $x1
     * @param int          $y1
     * @param int          $x2
     * @param int          $y2
     * @param bool         $filled
     *
     * @return DrawInterface
     */
    public function rectangle($color, $x1 = 0, $y1 = 0, $x2 = 10, $y2 = 10, $filled = true)
    {
        $callback = $filled ? 'imagefilledrectangle' : 'imagerectangle';
        call_user_func($callback, $this->_image->getResource(), $x1, $y1, $x2, $y2, $this->_image->allocateColor($color));

        return $this;
    }

    /**
     * Draws a line
     * It starts at ($x1, $y1) and ends at ($x2, $y2)
     *
     * @param string|array $color
     * @param int          $x1
     * @param int          $y1
     * @param int          $x2
     * @param int          $y2
     *
     * @return DrawInterface
     */
    public function line($color, $x1 = 0, $y1 = 0, $x2 = 10, $y2 = 10)
    {
        imageline($this->_image->getResource(), $x1, $y1, $x2, $y2, $this->_image->allocateColor($color));

        return $this;
    }
}