<?php

namespace Zweer\Image\Driver;


interface EngineInterface
{
    /**
     * The contructor
     * It only stores the $image argument.
     *
     * @param ImageInterface $image
     */
    public function __construct(ImageInterface $image);
}