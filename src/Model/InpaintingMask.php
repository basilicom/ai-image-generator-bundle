<?php

namespace Basilicom\AiImageGeneratorBundle\Model;

use Imagick;
use ImagickException;

class InpaintingMask
{
    private string $base64EncodedData = '';

    public function __construct(string $base64EncodedData)
    {
        $this->base64EncodedData = $base64EncodedData;
    }

    public function getData(bool $decode = false): string
    {
        return $decode ? base64_decode($this->base64EncodedData) : $this->base64EncodedData;
    }

    /**
     * @throws ImagickException
     */
    public function getResized(int $maxWidth, int $maxHeight, bool $decode = false, bool $bestFit = true): string
    {
        $newImage = new Imagick();
        $newImage->readImageBlob($this->getData(true));
        $newImage->resizeimage($maxWidth, $maxHeight, Imagick::FILTER_UNDEFINED, 1, $bestFit);

        return $decode ? $newImage->getImageBlob() : base64_encode($newImage->getImageBlob());
    }
}
