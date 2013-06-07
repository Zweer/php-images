<?php

namespace Zweer\Image\Effect;

use Zweer\Image\Engine\EngineInterface;

interface EffectInterface extends EngineInterface
{
    /**
     * Applies the negate filter
     *
     * @return EffectInterface
     */
    public function invert();

    /**
     * Applies the grayscale filter
     *
     * @return EffectInterface
     */
    public function desaturate();

    /**
     * Applies the brightness filter
     * $level should be between -100 and 100
     *
     * @param int $level
     *
     * @return EffectInterface
     */
    public function brightness($level);

    /**
     * Applies the contrast filter
     * $level should be between -100 and 100
     *
     * @param int $level
     *
     * @return EffectInterface
     */
    public function contrast($level);

    /**
     * Applies the colorize filter
     * $opacity must be between 0 and 100
     *
     * @param string|array $color
     *
     * @return EffectInterface
     */
    public function colorize($color);

    /**
     * Applies the edge detect filter
     *
     * @return EffectInterface
     */
    public function edges();

    /**
     * Applies the emboss filter
     *
     * @return EffectInterface
     */
    public function emboss();

    /**
     * Applies the (gaussian) blur filter
     *
     * @param string $type   [selective|gaussian]
     * @param int    $passes
     *
     * @return EffectInterface
     */
    public function blur($type = 'selective', $passes = 1);

    /**
     * Applies the mean removal filter
     *
     * @return EffectInterface
     */
    public function sketch();

    /**
     * Applies the smooth filter
     * $level should be between -8 and 8
     *
     * @param int $level
     *
     * @return EffectInterface
     */
    public function smooth($level);

    /**
     * Applies the pixelate filter
     *
     * @param int  $blockSize
     * @param bool $advanced
     *
     * @return EffectInterface
     */
    public function pixelate($blockSize = 10, $advanced = true);

    /**
     * Applies the sepia filter
     *
     * @return EffectInterface
     */
    public function sepia();

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