<?php

use Zweer\Image\Image;

class ImageTest extends PHPUnit_Framework_TestCase
{
    public function testContructorEmptySquared()
    {
        $width = 2;

        $img = Image::create($width);

        $this->assertInstanceOf('Zweer\\Image\\Driver\\Gd\\Image', $img);

        $this->assertInternalType('resource', $img->getResource());

        $this->assertInternalType('int', $img->getWidth());
        $this->assertEquals($width, $img->getWidth());

        $this->assertInternalType('int', $img->getHeight());
        $this->assertEquals($width, $img->getHeight());

        $this->assertInternalType('string', $img->getOrientation());
        $this->assertEquals(Image::ORIENTATION_SQUARE, $img->getOrientation());
    }

    public function testContructorEmptyPortrait()
    {
        $width = 2;
        $height = 3;

        $img = Image::create($width, $height);

        $this->assertInternalType('int', $img->getWidth());
        $this->assertEquals($width, $img->getWidth());

        $this->assertInternalType('int', $img->getHeight());
        $this->assertEquals($height, $img->getHeight());

        $this->assertInternalType('string', $img->getOrientation());
        $this->assertEquals(Image::ORIENTATION_PORTRAIT, $img->getOrientation());
    }
}
