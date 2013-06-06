<?php

namespace Zweer\Image\Effect;

use Zweer\Image\Engine\EngineAbstract;

abstract class EffectAbstract extends EngineAbstract implements EffectInterface
{
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
    public static function parseLevel($level, $factor = 2.55, $min = -100, $max = 100)
    {
        if ($level >= $min and $level <= $max) {
            return round($level * $factor);
        } else {
            throw new \InvalidArgumentException('The $level provided is not valid: it must be between ' . $min . ' and ' . $max);
        }
    }
}