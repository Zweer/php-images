<?php

use Zweer\Image\Image;
use Zweer\Image\Driver\ImageAbstract;

class ImageAbstractTest extends PHPUnit_Framework_TestCase
{
    public function testSquared()
    {
        $width = 2;

        $img = Image::create($width);

        $this->assertInternalType('string', $img->getOrientation());
        $this->assertEquals(Image::ORIENTATION_SQUARE, $img->getOrientation());
    }

    public function testPortrait()
    {
        $width = 2;
        $height = 3;

        $img = Image::create($width, $height);

        $this->assertInternalType('string', $img->getOrientation());
        $this->assertEquals(Image::ORIENTATION_PORTRAIT, $img->getOrientation());
    }

    public function testLandscape()
    {
        $width = 2;
        $height = 1;

        $img = Image::create($width, $height);

        $this->assertInternalType('string', $img->getOrientation());
        $this->assertEquals(Image::ORIENTATION_LANDSCAPE, $img->getOrientation());
    }

    public function testStaticChecks()
    {
        $img = Image::create(2);

        $this->assertTrue(ImageAbstract::isImageResource($img->getResource()));
    }
}
