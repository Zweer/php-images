<?php

use Zweer\Image\Image;

class ManipulateAbstractTest extends PHPUnit_Framework_TestCase
{
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
    }
}
