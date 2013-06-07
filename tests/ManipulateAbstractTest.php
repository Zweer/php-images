<?php

use Zweer\Image\Image;

class ManipulateAbstractTest extends PHPUnit_Framework_TestCase
{
    public function testManipulate()
    {
        $img = Image::create(2);

        $this->assertInstanceOf('\\Zweer\\Image\\Manipulate\\ManipulateAbstract', $img->manipulate());
    }

    public function testFlip()
    {
        $img = Image::make(__DIR__ . '/../examples/assets/ralph.jpg');
        $width = $img->getWidth();
        $height = $img->getHeight();
        $x = 700;
        $y = 300;
        $color = $img->pickColor($x, $y, 'int');

        $img->manipulate()->flip(Image::FLIP_HORIZONTAL);

        $this->assertEquals($width, $img->getWidth());
        $this->assertEquals($height, $img->getHeight());
        $this->assertEquals($color, $img->pickColor($width - $x - 1, $y, 'int'));

        $img->manipulate()->flip(Image::FLIP_VERTICAL);

        $this->assertEquals($width, $img->getWidth());
        $this->assertEquals($height, $img->getHeight());
        $this->assertEquals($color, $img->pickColor($width - $x - 1, $height - $y - 1, 'int'));
    }

    /**
     * @expectedException \Exception
     */
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

        $img->manipulate()->resize($resizeWidth + 1, $resizeHeight + 10, true, false);

        $this->assertEquals($resizeWidth, $img->getWidth());
        $this->assertEquals($resizeHeight, $img->getHeight());

        $img->manipulate()->resize($resizeWidth + 1, null, false, false);

        $this->assertEquals($resizeWidth, $img->getWidth());
        $this->assertEquals($resizeHeight, $img->getHeight());

        $img->manipulate()->resize(null, $resizeHeight + 10, false, false);

        $this->assertEquals($resizeWidth, $img->getWidth());
        $this->assertEquals($resizeHeight, $img->getHeight());

        // Throws an expected exception
        $img->manipulate()->resize(null, null, false, false);
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

        $img->manipulate()->resize('50%', '50%');

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

        $resizeWidth = $originalWidth;
        $resizeHeight = $originalHeight;

        $img->manipulate()->canvas($resizeWidth, $resizeHeight);

        $this->assertEquals($resizeWidth, $img->getWidth());
        $this->assertEquals($resizeHeight, $img->getHeight());
    }

    public function testCanvasAnchor()
    {
        $width = 20;

        $black = '#000000';
        $white = '#ffffff';

        // Top Left
        $img = Image::create($width, $width, $black);
        $img->manipulate()->canvas($width + 1, $width + 1, 'top left', $white);

        $this->assertEquals($black, strtolower($img->pickColor(0, 0, 'hex')));
        $this->assertEquals($black, strtolower($img->pickColor($width / 2, 0, 'hex')));
        $this->assertEquals($white, strtolower($img->pickColor($width, 0, 'hex')));
        $this->assertEquals($black, strtolower($img->pickColor(0, $width / 2, 'hex')));
        $this->assertEquals($white, strtolower($img->pickColor(0, $width, 'hex')));

        // Top Center
        $img = Image::create($width, $width, $black);
        $img->manipulate()->canvas($width + 2, $width + 1, 'top center', $white);

        $this->assertEquals($white, strtolower($img->pickColor(0, 0, 'hex')));
        $this->assertEquals($black, strtolower($img->pickColor($width / 2, 0, 'hex')));
        $this->assertEquals($white, strtolower($img->pickColor($width + 1, 0, 'hex')));
        $this->assertEquals($white, strtolower($img->pickColor($width / 2, $width, 'hex')));

        // Top Right
        $img = Image::create($width, $width, $black);
        $img->manipulate()->canvas($width + 1, $width + 1, 'top right', $white);

        $this->assertEquals($white, strtolower($img->pickColor(0, 0, 'hex')));
        $this->assertEquals($black, strtolower($img->pickColor($width / 2, 0, 'hex')));
        $this->assertEquals($black, strtolower($img->pickColor($width, 0, 'hex')));
        $this->assertEquals($black, strtolower($img->pickColor($width, $width / 2, 'hex')));
        $this->assertEquals($white, strtolower($img->pickColor($width, $width, 'hex')));

        // Middle Left
        $img = Image::create($width, $width, $black);
        $img->manipulate()->canvas($width + 1, $width + 2, 'middle left', $white);

        $this->assertEquals($white, strtolower($img->pickColor(0, 0, 'hex')));
        $this->assertEquals($black, strtolower($img->pickColor(0, $width / 2, 'hex')));
        $this->assertEquals($white, strtolower($img->pickColor(0, $width + 1, 'hex')));
        $this->assertEquals($white, strtolower($img->pickColor($width, $width / 2, 'hex')));

        // Middle Center
        $img = Image::create($width, $width, $black);
        $img->manipulate()->canvas($width + 2, $width + 2, 'middle center', $white);

        $this->assertEquals($white, strtolower($img->pickColor(0, 0, 'hex')));
        $this->assertEquals($white, strtolower($img->pickColor(0, $width + 1, 'hex')));
        $this->assertEquals($white, strtolower($img->pickColor($width + 1, 0, 'hex')));
        $this->assertEquals($white, strtolower($img->pickColor($width + 1, $width + 1, 'hex')));
        $this->assertEquals($black, strtolower($img->pickColor(1, 1, 'hex')));

        // Middle Right
        $img = Image::create($width, $width, $black);
        $img->manipulate()->canvas($width + 1, $width + 2, 'middle right', $white);

        $this->assertEquals($white, strtolower($img->pickColor($width, 0, 'hex')));
        $this->assertEquals($black, strtolower($img->pickColor($width, $width / 2, 'hex')));
        $this->assertEquals($white, strtolower($img->pickColor($width, $width + 1, 'hex')));
        $this->assertEquals($white, strtolower($img->pickColor(0, $width / 2, 'hex')));

        // Bottom Left
        $img = Image::create($width, $width, $black);
        $img->manipulate()->canvas($width + 1, $width + 1, 'bottom left', $white);

        $this->assertEquals($white, strtolower($img->pickColor(0, 0, 'hex')));
        $this->assertEquals($black, strtolower($img->pickColor(0, $width / 2, 'hex')));
        $this->assertEquals($black, strtolower($img->pickColor(0, $width, 'hex')));
        $this->assertEquals($black, strtolower($img->pickColor($width / 2, $width, 'hex')));
        $this->assertEquals($white, strtolower($img->pickColor($width, $width, 'hex')));

        // Bottom Center
        $img = Image::create($width, $width, $black);
        $img->manipulate()->canvas($width + 2, $width + 1, 'bottom center', $white);

        $this->assertEquals($white, strtolower($img->pickColor($width / 2, 0, 'hex')));
        $this->assertEquals($white, strtolower($img->pickColor(0, $width, 'hex')));
        $this->assertEquals($black, strtolower($img->pickColor($width / 2, $width, 'hex')));
        $this->assertEquals($white, strtolower($img->pickColor($width + 1, $width, 'hex')));

        // Bottom Right
        $img = Image::create($width, $width, $black);
        $img->manipulate()->canvas($width + 1, $width + 1, 'bottom right', $white);

        $this->assertEquals($white, strtolower($img->pickColor($width, 0, 'hex')));
        $this->assertEquals($black, strtolower($img->pickColor($width, $width / 2, 'hex')));
        $this->assertEquals($white, strtolower($img->pickColor(0, $width, 'hex')));
        $this->assertEquals($black, strtolower($img->pickColor($width / 2, $width, 'hex')));
        $this->assertEquals($black, strtolower($img->pickColor($width, $width, 'hex')));
    }

    public function testCrop()
    {
        $originalWidth = 20;
        $originalHeight = 30;

        $cropWidth = 10;
        $cropHeight = 20;

        $img = Image::create($originalWidth, $originalHeight);
        $img->manipulate()->crop($cropWidth, $cropHeight);

        $this->assertEquals($cropWidth, $img->getWidth());
        $this->assertEquals($cropHeight, $img->getHeight());

        $img->manipulate()->crop($cropWidth);

        $this->assertEquals($cropWidth, $img->getWidth());
        $this->assertEquals($cropWidth, $img->getHeight());
    }
}
