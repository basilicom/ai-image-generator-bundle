<?php

namespace Basilicom\AiImageGeneratorBundle\Model;

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
}
