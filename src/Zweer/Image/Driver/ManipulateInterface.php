<?php

namespace Zweer\Image\Driver;

interface ManipulateInterface
{
    /**
     * The contructor
     * It only stores the $image argument.
     *
     * @param ImageInterface $image
     */
    public function __construct(ImageInterface $image);

    /**
     * Flips the image horizontally (default) or vertically
     *
     * @param string $mode
     *
     * @return ManipulateInterface
     */
    public function flip($mode = null);
}