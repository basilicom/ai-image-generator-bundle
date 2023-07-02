<?php

namespace Basilicom\AiImageGeneratorBundle\Config\Model;

use Basilicom\AiImageGeneratorBundle\Config\AbstractConfiguration;

class DreamStudioApiConfig extends AbstractConfiguration
{
    private string $apiKey;

    public function __construct(string $baseUrl, string $model, int $steps, string $apiKey)
    {
        parent::__construct($baseUrl, $model, $steps);
        $this->apiKey = $apiKey;
    }

    public function getApiKey(): string
    {
        return $this->apiKey;
    }
}
