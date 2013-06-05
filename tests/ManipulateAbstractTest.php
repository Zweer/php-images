<?php

use Zweer\Image\Image;

class ManipulateAbstractTest extends PHPUnit_Framework_TestCase
{
    public function testManipulate()
    {
        $img = Image::create(2);

        $this->assertInstanceOf('\\Zweer\\Image\\Driver\\ManipulateAbstract', $img->manipulate());
    }

    public function testFlip()
    {
        $width = 20;
        $height = 30;

        $img = Image::create($width, $height);
        $img->manipulate()->flip(Image::FLIP_HORIZONTAL);

        $this->assertEquals($width, $img->getWidth());
        $this->assertEquals($height, $img->getHeight());

        $img->manipulate()->flip(Image::FLIP_VERTICAL);

        $this->assertEquals($width, $img->getWidth());
        $this->assertEquals($height, $img->getHeight());
    }

    public function testResize()
    {
        $originalWidth = 20;
        $originalHeight = 30;

        $resizeWidth = 10;
        $resizeHeight = 20;

        $img = Image::create($originalWidth, $originalHeight);
        $img->manipulate()->resize($resizeWidth, $resizeHeight, false);

        $this->assertEquals($resizeWidth, $img->getWidth());
        $this->assertEquals($resizeHeight, $img->getHeight());

        $resizeWidth /= 2;
        $resizeHeight /= 2;

        $img->manipulate()->resize($resizeWidth);

        $this->assertEquals($resizeWidth, $img->getWidth());
        $this->assertEquals($resizeHeight, $img->getHeight());

        $resizeWidth *= 2;
        $resizeHeight *= 2;

        $img->manipulate()->resize($resizeWidth, $resizeHeight, true, true);

        $this->assertEquals($resizeWidth, $img->getWidth());
        $this->assertEquals($resizeHeight, $img->getHeight());

        $img->manipulate()->resize($resizeWidth + 1, $resizeHeight + 1, true, false);

        $this->assertEquals($resizeWidth, $img->getWidth());
        $this->assertEquals($resizeHeight, $img->getHeight());
    }

    public function testRelativeResize()
    {
        $originalWidth = 20;
        $originalHeight = 30;

        $deltaWidth = '+2';
        $deltaHeight = -3;

        $resizedWidth = $originalWidth + (int) $deltaWidth;
        $resizedHeight = $originalHeight + $deltaHeight;

        $img = Image::create($originalWidth, $originalHeight);
        $img->manipulate()->resize($deltaWidth, $deltaHeight, false);

        $this->assertEquals($resizedWidth, $img->getWidth());
        $this->assertEquals($resizedHeight, $img->getHeight());

        $img->manipulate()->resize('50%');

        $this->assertEquals(intval($resizedWidth / 2), $img->getWidth());
        $this->assertEquals(intval($resizedHeight / 2), $img->getHeight());
    }

    public function testCanvas()
    {
        $originalWidth = 20;
        $originalHeight = 30;

        $resizeWidth = 10;
        $resizeHeight = 20;

        $img = Image::create($originalWidth, $originalHeight);
        $img->manipulate()->canvas($resizeWidth, $resizeHeight);

        $this->assertEquals($resizeWidth, $img->getWidth());
        $this->assertEquals($resizeHeight, $img->getHeight());
    }
}
