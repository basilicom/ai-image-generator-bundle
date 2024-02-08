<?php

namespace Basilicom\AiImageGeneratorBundle\Model;

use Imagick;
use ImagickException;
use ImagickPixel;

class InpaintingMask
{
    public const MODE_CONTAIN = 'contain';
    public const MODE_RESIZE = 'resize';

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
    public function getResized(
        int $maxWidth,
        int $maxHeight,
        bool $decode = false,
        bool $bestFit = true,
        bool $useAlpha = false,
        bool $invert = false,
        string $mode = self::MODE_CONTAIN
    ): string {
        $image = new Imagick();
        $image->readImageBlob($this->getData(true));
        $image->resizeimage($maxWidth, $maxHeight, Imagick::FILTER_UNDEFINED, 1, $bestFit);

        $backgroundFill = new Imagick();
        if ($mode === self::MODE_RESIZE) {
            $posX = ($maxWidth - $image->getImageWidth()) / 2;
            $posY = ($maxHeight - $image->getImageHeight()) / 2;

            $backgroundFill->newImage($maxWidth, $maxHeight, new ImagickPixel('transparent'));
            $backgroundFill->compositeImage($image, Imagick::COMPOSITE_OVER, $posX, $posY);
            $backgroundFill->thumbnailImage($maxWidth, $maxHeight, true, true);
            $backgroundFill->setImageFormat('png');

            $image = $backgroundFill;
        }

        if ($invert) {
            $image->negateImage(true, Imagick::CHANNEL_DEFAULT);
        }

        if ($useAlpha) {
            $image->setImageAlphaChannel(Imagick::ALPHACHANNEL_SET);
            $image->transparentPaintImage(new ImagickPixel('white'), 0, 10, false);
        }

        $image->blurImage(5, 3, Imagick::CHANNEL_DEFAULT);

        $imageBlob = $decode ? $image->getImageBlob() : base64_encode($image->getImageBlob());

        $image->destroy();
        $backgroundFill->destroy();

        return $imageBlob;
    }
}
