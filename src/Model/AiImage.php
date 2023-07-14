<?php

namespace Basilicom\AiImageGeneratorBundle\Model;

use Pimcore\Model\Asset;

class AiImage
{
    private string $data;
    private array $metadata;

    public function setData(string $data): void
    {
        $this->data = $data;
    }

    public function getData(bool $decode = false): string
    {
        return $decode ? base64_decode($this->data) : $this->data;
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

    public static function fromAsset(Asset\Image $asset): self
    {
        $instance = new self();
        $instance->setData(base64_encode($asset->getData()));
        foreach ((array)$asset->getMetadata() as $metadata) {
            $instance->setMetadata($metadata['name'], $metadata['data']);
        }

        return $instance;
    }
}
