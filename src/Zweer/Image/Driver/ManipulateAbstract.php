<?php

namespace Zweer\Image\Driver;

abstract class ManipulateAbstract implements ManipulateInterface
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
     * Modify wrapper
     * Used in many function such as resize and grab
     *
     * @param int $dst_x Destination X coord
     * @param int $dst_y Destination Y coord
     * @param int $src_x Source X coord
     * @param int $src_y Source Y coord
     * @param int $dst_w Destination width
     * @param int $dst_h Destination height
     * @param int $src_w Source width
     * @param int $src_h Source height
     *
     * @return ManipulateInterface
     */
    protected function modify($dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h)
    {
        // create new image
        $image = imagecreatetruecolor($dst_w, $dst_h);

        // preserve transparency
        imagealphablending($image, false);
        imagesavealpha($image, true);

        // copy content from resource
        imagecopyresampled($image, $this->_image->getResource(), $dst_x , $dst_y , $src_x , $src_y , $dst_w , $dst_h , $src_w , $src_h);

        // set new content as recource
        $this->_image->setResource($image);

        return $this;
    }
}