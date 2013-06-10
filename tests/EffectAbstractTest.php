<?php

use Zweer\Image\Image;

class EffectAbstractTest extends PHPUnit_Framework_TestCase
{
    public function testSepia()
    {
        $img = Image::make(__DIR__ . '/../examples/assets/ralph.jpg');

        $this->assertInstanceOf('Zweer\\Image\\Effect\\EffectAbstract', $img->effect()->sepia());

        $img = Image::make(__DIR__ . '/../examples/assets/ralph.gif');

        $this->assertInstanceOf('Zweer\\Image\\Effect\\EffectAbstract', $img->effect()->sepia());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testLevelException()
    {
        \Zweer\Image\Effect\EffectAbstract::parseLevel(120);
    }
}
