<?php

use Zweer\Image\Image;

class EffectAbstractTest extends PHPUnit_Framework_TestCase
{
    public function testManipulate()
    {
        $img = Image::create(2);

        $this->assertInstanceOf('\\Zweer\\Image\\Engine\\EngineAbstract', $img->manipulate());
    }

    public function testEffect()
    {
        $img = Image::create(2);

        $this->assertInstanceOf('\\Zweer\\Image\\Engine\\EngineAbstract', $img->effect());
    }

    public function testDraw()
    {
        $img = Image::create(2);

        $this->assertInstanceOf('\\Zweer\\Image\\Engine\\EngineAbstract', $img->draw());
    }

    public function testToString()
    {
        $img = Image::create(2);

        $this->assertEquals((string) $img, (string) $img->manipulate());
    }
}
