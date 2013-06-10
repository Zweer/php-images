<?php

namespace Zweer\Image\Driver\Gd;

use Zweer\Image\Effect\EffectAbstract;
use Zweer\Image\Effect\EffectInterface;
use Zweer\Image\Engine\EngineInterface;

class Effect extends EffectAbstract
{
    /**
     * Applies a filter to the image
     *
     * @param int   $filter
     * @param mixed $argument1
     * @param mixed $argument2
     * @param mixed $argument3
     * @param mixed $argument4
     *
     * @return EffectInterface
     */
    protected function _filter($filter, $argument1 = null, $argument2 = null, $argument3 = null, $argument4 = null)
    {
        @imagefilter($this->_image->getResource(), $filter, $argument1, $argument2, $argument3, $argument4);

        return $this;
    }

    /**
     * Applies the negate filter
     *
     * @return EngineInterface
     */
    public function invert()
    {
        return $this->_filter(IMG_FILTER_NEGATE);
    }

    /**
     * Applies the grayscale filter
     *
     * @return EngineInterface
     */
    public function desaturate()
    {
        return $this->_filter(IMG_FILTER_GRAYSCALE);
    }

    /**
     * Applies the brightness filter
     * $level should be between -100 and 100
     *
     * @param int $level
     *
     * @return EngineInterface
     */
    public function brightness($level)
    {
        $level = static::parseLevel($level);

        return $this->_filter(IMG_FILTER_BRIGHTNESS, $level);
    }

    /**
     * Applies the contrast filter
     * $level should be between -100 and 100
     *
     * @param int $level
     *
     * @return EngineInterface
     */
    public function contrast($level)
    {
        $level = static::parseLevel($level, 1);

        return $this->_filter(IMG_FILTER_CONTRAST, $level);
    }

    /**
     * Applies the colorize filter
     * $opacity must be between 0 and 100
     *
     * @param string|array $color
     *
     * @return EngineInterface
     */
    public function colorize($color)
    {
        list($red, $green, $blue, $alpha) = Image::parseColor($color);

        return $this->_filter(IMG_FILTER_COLORIZE, $red, $green, $blue, $alpha);
    }

    /**
     * Applies the edge detect filter
     *
     * @return EngineInterface
     */
    public function edges()
    {
        return $this->_filter(IMG_FILTER_EDGEDETECT);
    }

    /**
     * Applies the emboss filter
     *
     * @return EngineInterface
     */
    public function emboss()
    {
        return $this->_filter(IMG_FILTER_EMBOSS);
    }

    /**
     * Applies the (gaussian) blur filter
     *
     * @param string $type   [selective|gaussian]
     * @param int    $passes
     *
     * @return EngineInterface
     */
    public function blur($type = 'selective', $passes = 1)
    {
        $type = $type == 'selective' ? IMG_FILTER_SELECTIVE_BLUR : IMG_FILTER_GAUSSIAN_BLUR;

        for ($i = 0; $i < $passes; ++$i) {
            $this->_filter($type);
        }

        return $this;
    }

    /**
     * Applies the mean removal filter
     *
     * @return EngineInterface
     */
    public function sketch()
    {
        return $this->_filter(IMG_FILTER_MEAN_REMOVAL);
    }

    /**
     * Applies the smooth filter
     * $level should be between -100 and 100
     *
     * @param int $level
     *
     * @return EngineInterface
     */
    public function smooth($level)
    {
        $level = static::parseLevel($level, .08);

        return $this->_filter(IMG_FILTER_SMOOTH, $level);
    }

    /**
     * Applies the pixelate filter
     *
     * @param int  $blockSize
     * @param bool $advanced
     *
     * @return EngineInterface
     */
    public function pixelate($blockSize = 10, $advanced = true)
    {
        return $this->_filter(IMG_FILTER_PIXELATE, $blockSize, $advanced);
    }
}