<?php

namespace Basilicom\AiImageGeneratorBundle\Model;

use Imagick;
use ImagickException;
use Pimcore\Model\Asset;

class AiImage
{
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
    public function getResizedImage(int $maxWidth, int $maxHeight, bool $decode = false, bool $bestFit = true): string
    {
        $newImage = new Imagick();
        $newImage->readImageBlob($this->getData(true));
        $newImage->resizeimage($maxWidth, $maxHeight, Imagick::FILTER_UNDEFINED, 1, $bestFit);

        return $decode ? $newImage->getImageBlob() : base64_encode($newImage->getImageBlob());
    }

    /**
     * @throws ImagickException
     */
    public function getResizedMask(int $maxWidth, int $maxHeight, bool $decode = false, bool $bestFit = true): string
    {
        $newImage = new Imagick();
        $newImage->readImageBlob($this->getMask(true));
        $newImage->resizeimage($maxWidth, $maxHeight, Imagick::FILTER_UNDEFINED, 1, $bestFit);

        return $decode ? $newImage->getImageBlob() : base64_encode($newImage->getImageBlob());
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
