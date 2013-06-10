<?php

use Zweer\Image\Image;

class EffectTest extends PHPUnit_Framework_TestCase
{
    public function testEffects()
    {
        $img = Image::make(__DIR__ . '/../../examples/assets/ralph.jpg');

        $this->assertInstanceOf('Zweer\\Image\\Driver\\Gd\\Effect', $img->effect()->invert());
        $this->assertInstanceOf('Zweer\\Image\\Driver\\Gd\\Effect', $img->effect()->desaturate());
        $this->assertInstanceOf('Zweer\\Image\\Driver\\Gd\\Effect', $img->effect()->brightness(50));
        $this->assertInstanceOf('Zweer\\Image\\Driver\\Gd\\Effect', $img->effect()->contrast(-50));
        $this->assertInstanceOf('Zweer\\Image\\Driver\\Gd\\Effect', $img->effect()->colorize('f00'));
        $this->assertInstanceOf('Zweer\\Image\\Driver\\Gd\\Effect', $img->effect()->edges());
        $this->assertInstanceOf('Zweer\\Image\\Driver\\Gd\\Effect', $img->effect()->emboss());
        $this->assertInstanceOf('Zweer\\Image\\Driver\\Gd\\Effect', $img->effect()->blur());
        $this->assertInstanceOf('Zweer\\Image\\Driver\\Gd\\Effect', $img->effect()->sketch());
        $this->assertInstanceOf('Zweer\\Image\\Driver\\Gd\\Effect', $img->effect()->smooth(20));
        $this->assertInstanceOf('Zweer\\Image\\Driver\\Gd\\Effect', $img->effect()->pixelate());
    }
}
