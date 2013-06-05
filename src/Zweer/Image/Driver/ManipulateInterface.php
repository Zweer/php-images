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

    /**
     * Resizes the image
     * You can decide if preserving the ratio and upsizing the image.
     *
     * @param int  $width
     * @param int  $height
     * @param bool $ratio
     * @param bool $upsize
     *
     * @return ManipulateInterface
     * @throws \Exception
     */
    public function resize($width = null, $height = null, $ratio = true, $upsize = true);
}