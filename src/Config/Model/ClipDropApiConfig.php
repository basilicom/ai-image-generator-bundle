<?php

namespace Basilicom\AiImageGeneratorBundle\Config\Model;

use Basilicom\AiImageGeneratorBundle\Config\ServiceConfiguration;

class ClipDropApiConfig extends ServiceConfiguration
{
    protected ?string $name = 'ClipDrop';
    protected string $apiKey;

    public function __construct(string $baseUrl, string $apiKey)
    {
        parent::__construct(
            baseUrl: $baseUrl,
            model: '',
            inpaintModel: '',
            steps: 10,
            upscaler: ''
        );
        $this->apiKey = $apiKey;
    }

    public function getApiKey(): string
    {
        return $this->apiKey;
    }
}
