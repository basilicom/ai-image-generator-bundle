<?php

namespace Basilicom\AiImageGeneratorBundle\Config;

class BrandConfiguration
{
    private array $colorScheme;

    public function __construct(array $colorScheme)
    {
        $this->colorScheme = $colorScheme;
    }

    public function getColorScheme(): array
    {
        return $this->colorScheme;
    }
}
