<?php

namespace Basilicom\AiImageGeneratorBundle\Model;

use Imagick;
use ImagickException;
use ImagickPixel;
use Pimcore\Model\Asset;

class AiImage
{
    public const MODE_CONTAIN = 'contain';
    public const MODE_RESIZE = 'resize';

    private string $base64EncodedData;
    private string $base64EncodedMaskData;
    private array $metadata;

    public function setData(string $base64EncodedData): void
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
    public function getResizedImage(
        int $maxWidth,
        int $maxHeight,
        bool $decode = false,
        bool $bestFit = true,
        string $mode = self::MODE_CONTAIN
    ): string {
        $image = new Imagick();
        $image->readImageBlob($this->getData(true));

        if ($mode === self::MODE_RESIZE) {
            $image->thumbnailImage($maxWidth, $maxHeight, true, true);
        } else {
            $image->resizeimage($maxWidth, $maxHeight, Imagick::FILTER_UNDEFINED, 1, $bestFit);
        }

        $imageBlob = $decode ? $image->getImageBlob() : base64_encode($image->getImageBlob());
        $image->destroy();

        return $imageBlob;
    }

    /**
     * Inpaint-APIs will mainly edit white or transparent pixels
     * @throws ImagickException
     */
    public function getResizedMask(
        int $maxWidth,
        int $maxHeight,
        bool $decode = false,
        bool $bestFit = true,
        bool $useAlpha = false,
        bool $invert = false,
        string $mode = self::MODE_CONTAIN
    ): string {
        $image = new Imagick();
        $image->readImageBlob($this->getMask(true));
        $image->resizeimage($maxWidth, $maxHeight, Imagick::FILTER_UNDEFINED, 1, $bestFit);

        $backgroundFill = new Imagick();
        if ($mode === self::MODE_RESIZE) {
            $posX = ($maxWidth - $image->getImageWidth()) / 2;
            $posY = ($maxHeight - $image->getImageHeight()) / 2;

            $backgroundFill->newImage($maxWidth, $maxHeight, new ImagickPixel('black'));
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

    public function setMask(string $data): void
    {
        $this->base64EncodedMaskData = $data;
    }

    public function getMask(bool $decode = false): string
    {
        return $decode ? base64_decode($this->base64EncodedMaskData) : $this->base64EncodedMaskData;
    }

    public function setMetadata(string $key, mixed $value): void
    {
        $this->metadata[$key] = $value;
    }

    public function getMetadata(string $key): mixed
    {
        return $this->metadata[$key] ?? null;
    }

    public function getAllMetadata(): array
    {
        return $this->metadata;
    }

    public static function fromAsset(Asset\Image $asset, bool $createMask = false): self
    {
        $instance = new self();
        foreach ((array)$asset->getMetadata() as $metadata) {
            $instance->setMetadata($metadata['name'], $metadata['data']);
        }

        $instance->setData(base64_encode($asset->getData()));

        if ($createMask) {
            $maskImage = new Imagick($asset->getLocalFile());
            $maskImage->setImageAlphaChannel(Imagick::ALPHACHANNEL_EXTRACT);

            $instance->setMask(base64_encode($maskImage->getImageBlob()));
        }

        return $instance;
    }
}
