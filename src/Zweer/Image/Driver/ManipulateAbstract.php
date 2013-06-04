<?php

namespace Zweer\Image\Driver;

abstract class ManipulateAbstract implements ManipulateInterface
{
    /**
     * @var ImageInterface
     */
    protected $_image;

    public function __construct(ImageInterface $image)
    {
        $this->_image = $image;
    }
}