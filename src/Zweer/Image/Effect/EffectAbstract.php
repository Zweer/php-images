<?php

namespace Zweer\Image\Effect;

use Zweer\Image\Engine\EngineAbstract;

abstract class EffectAbstract extends EngineAbstract implements EffectInterface
{
    /**
     * Applies the sepia filter
     *
     * @return EffectInterface
     */
    public function sepia()
    {
        foreach ($this->_image->pickColors() as $index => $color) {
            $red   = ($color['red'] * .393 + $color['green'] * .769 + $color['blue'] * .189) / 1.351;
            $green = ($color['red'] * .349 + $color['green'] * .686 + $color['blue'] * .168) / 1.203;
            $blue  = ($color['red'] * .272 + $color['green'] * .534 + $color['blue'] * .131) / 2.140;

            $this->_image->setColor($index, $red, $green, $blue, $color['alpha']);
        }

        return $this;
    }

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