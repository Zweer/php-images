<?php

use Zweer\Image\Image;

class ImageTest extends PHPUnit_Framework_TestCase
{
    public function testContructorEmpty()
    {
        $width = 2;
        $height = 3;

        $img = Image::create($width, $height);

        $this->assertInstanceOf('Zweer\\Image\\Driver\\Gd\\Image', $img);

        $this->assertInternalType('resource', $img->getResource());

        $this->assertInternalType('int', $img->getWidth());
        $this->assertEquals($width, $img->getWidth());

        $this->assertInternalType('int', $img->getHeight());
        $this->assertEquals($height, $img->getHeight());
    }
}
