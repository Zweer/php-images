<?php

namespace Zweer\Image\Effect;

use Zweer\Image\Engine\EngineInterface;

interface EffectInterface extends EngineInterface
{
    /**
     * Applies the negate filter
     *
     * @return EngineInterface
     */
    public function invert();

    /**
     * Applies the grayscale filter
     *
     * @return EngineInterface
     */
    public function desaturate();

    /**
     * Applies the brightness filter
     * $level should be between -100 and 100
     *
     * @param int $level
     *
     * @return EngineInterface
     */
    public function brightness($level);

    /**
     * Applies the contrast filter
     * $level should be between -100 and 100
     *
     * @param int $level
     *
     * @return EngineInterface
     */
    public function contrast($level);

    /**
     * Applies the colorize filter
     * $opacity must be between 0 and 100
     *
     * @param string|array $color
     *
     * @return EngineInterface
     */
    public function colorize($color);

    /**
     * Applies the edge detect filter
     *
     * @return EngineInterface
     */
    public function edges();

    /**
     * Applies the emboss filter
     *
     * @return EngineInterface
     */
    public function emboss();

    /**
     * Applies the (gaussian) blur filter
     *
     * @param string $type   [selective|gaussian]
     * @param int    $passes
     *
     * @return EngineInterface
     */
    public function blur($type = 'selective', $passes = 1);

    /**
     * Applies the mean removal filter
     *
     * @return EngineInterface
     */
    public function sketch();

    /**
     * Applies the smooth filter
     * $level should be between -8 and 8
     *
     * @param int $level
     *
     * @return EngineInterface
     */
    public function smooth($level);

    /**
     * Applies the pixelate filter
     *
     * @param int  $blockSize
     * @param bool $advanced
     *
     * @return EngineInterface
     */
    public function pixelate($blockSize = 10, $advanced = true);

    //TODO: sepia

    /**
     * Checks the $level to be between $min and $max
     *
     * @param int   $level
     * @param float $factor
     * @param int   $min
     * @param int   $max
     *
     * @throws \InvalidArgumentException
     * @return int
     */
    public static function parseLevel($level, $factor = 2.55, $min = -100, $max = 100);
}