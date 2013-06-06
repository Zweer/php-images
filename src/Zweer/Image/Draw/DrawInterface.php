<?php

namespace Zweer\Image\Draw;

use Zweer\Image\Engine\EngineInterface;

interface DrawInterface extends EngineInterface
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
    public function rectangle($color, $x1 = 0, $y1 = 0, $x2 = 10, $y2 = 10, $filled = true);
}