<?php

namespace Basilicom\AiImageGeneratorBundle\Config\Model\Prompting;

use Basilicom\AiImageGeneratorBundle\Config\Model\PromptEnhancementConfig;

class NullApiConfig extends PromptEnhancementConfig
{
    public function __construct(string $baseUrl = '')
    {
        parent::__construct($baseUrl);
    }
}
