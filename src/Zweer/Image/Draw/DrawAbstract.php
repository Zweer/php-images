<?php

namespace Zweer\Image\Draw;

use Zweer\Image\Engine\EngineAbstract;

abstract class DrawAbstract extends EngineAbstract implements DrawInterface
{
    /**
     * Draws a circle
     * It starts at ($x1, $y1) with $radius
     *
     * @param string|array $color
     * @param int          $x1
     * @param int          $y1
     * @param int          $radius
     * @param bool         $filled
     *
     * @return DrawInterface
     */
    public function circle($color, $x1 = 0, $y1 = 0, $radius = 5, $filled = true)
    {
        return $this->ellipse($color, $x1, $y1, $radius * 2, $radius * 2, $filled);
    }
}