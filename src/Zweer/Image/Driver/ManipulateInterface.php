<?php

namespace Zweer\Image\Driver;

interface ManipulateInterface
{
    public function __construct(ImageInterface $image);

    //public function modify($dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h);
}