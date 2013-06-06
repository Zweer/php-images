<?php

namespace Zweer\Image\Engine;

use Zweer\Image\Image\ImageInterface;


interface EngineInterface
{
    /**
     * The contructor
     * It only stores the $image argument.
     *
     * @param ImageInterface $image
     */
    public function __construct(ImageInterface $image);

    /**
     * Returns the string representation of the image
     *
     * @return string
     */
    public function __toString();
}