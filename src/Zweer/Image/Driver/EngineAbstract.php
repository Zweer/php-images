<?php

namespace Zweer\Image\Driver;

abstract class EngineAbstract implements EngineInterface
{
    /**
     * @var ImageInterface
     */
    protected $_image;

    /**
     * The contructor
     * It only stores the $image argument.
     *
     * @param ImageInterface $image
     */
    public function __construct(ImageInterface $image)
    {
        $this->_image = $image;
    }

    /**
     * Returns the string representation of the image
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->_image;
    }
}