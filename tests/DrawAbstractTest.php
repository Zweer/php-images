<?php

use Zweer\Image\Image;

class DrawAbstractTest extends PHPUnit_Framework_TestCase
{
    public function testCircle()
    {
        $radius = 10;

        $black = '#000000';
        $white = '#ffffff';

        $img = Image::create($radius * 2, null, $white);
        $img->draw()->circle($black, $radius, $radius, $radius);

        $this->assertEquals($white, strtolower($img->pickColor(0, 0, 'hex')));
        $this->assertEquals($black, strtolower($img->pickColor($radius, 0, 'hex')));
        $this->assertEquals($black, strtolower($img->pickColor(0, $radius, 'hex')));
        $this->assertEquals($black, strtolower($img->pickColor($radius, $radius, 'hex')));
        $this->assertEquals($white, strtolower($img->pickColor($radius * 2 - 1, 0, 'hex')));
    }
}
