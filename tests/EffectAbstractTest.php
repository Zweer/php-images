<?php

use Zweer\Image\Image;

class EffectAbstractTest extends PHPUnit_Framework_TestCase
{
    public function testSepia()
    {
        $img = Image::make(__DIR__ . '/../examples/assets/ralph.jpg');

        $this->assertInstanceOf('Zweer\\Image\\Effect\\EffectAbstract', $img->effect()->sepia());
    }
}
