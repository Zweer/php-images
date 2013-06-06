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
    public function line($color, $x1 = 0, $y1 = 0, $x2 = 10, $y2 = 10);

    /**
     * Draws an ellipse
     * It starts at ($x1, $y1), $width x $height
     *
     * @param string|array $color
     * @param int          $x1
     * @param int          $y1
     * @param int          $width
     * @param int          $height
     * @param bool         $filled
     *
     * @return DrawInterface
     */
    public function ellipse($color, $x1 = 0, $y1 = 0, $width = 10, $height = 10, $filled = true);

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
    public function circle($color, $x1 = 0, $y1 = 0, $radius = 5, $filled = true);

    /**
     * Writes text in the current image
     *
     * @param string       $text
     * @param int          $positionX
     * @param int          $positionY
     * @param int          $size
     * @param string|array $color
     * @param int          $angle
     * @param string       $fontfile
     *
     * @return DrawInterface
     */
    public function text($text, $positionX = 0, $positionY = 0, $size = 16, $color = '000', $angle = 0, $fontfile = null);
}