<?php

use Zweer\Image\Image;

class ImageWrapperTest extends PHPUnit_Framework_TestCase
{
    public function testCreate()
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

    public function testMake()
    {
        $img = Image::make('examples/assets/ralph.jpg');

        $this->assertInstanceOf('Zweer\\Image\\Driver\\Gd\\Image', $img);

        $this->assertInternalType('resource', $img->getResource());
    }
}
