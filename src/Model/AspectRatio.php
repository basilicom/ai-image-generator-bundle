<?php

namespace Basilicom\AiImageGeneratorBundle\Model;

class AspectRatio
{
    private string $aspectRatio;
    private int $width;
    private int $height;

    public function __construct(string $aspectRatio, int $width, int $height)
    {
        $this->aspectRatio = $aspectRatio;
        $this->width = $width;
        $this->height = $height;
    }

    public function getAspectRatio(): string
    {
        return $this->aspectRatio;
    }

    public function getWidth(): int
    {
        return $this->width;
    }

    public function getHeight(): int
    {
        return $this->height;
    }
}
