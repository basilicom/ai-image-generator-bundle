<?php

namespace Basilicom\AiImageGeneratorBundle\Strategy\Prompting;

use Basilicom\AiImageGeneratorBundle\Config\ConfigurationService;
use Basilicom\AiImageGeneratorBundle\Config\Model\PromptEnhancementConfig;
use Basilicom\AiImageGeneratorBundle\Model\Prompt;
use Basilicom\AiImageGeneratorBundle\Service\Prompt\PromptPreset;
use Basilicom\AiImageGeneratorBundle\Service\RequestService;

abstract class Strategy
{
    protected PromptEnhancementConfig $promptEnhancementConfig;
    protected RequestService $requestService;
    protected PromptPreset $promptPreset;

    public function __construct(
        ConfigurationService $configurationService,
        RequestService       $requestService,
        PromptPreset         $promptPreset
    ) {
        $this->promptEnhancementConfig = $configurationService->getPromptEnhancementConfiguration();
        $this->requestService = $requestService;
        $this->promptPreset = $promptPreset;
    }

    public function enhancePrompt(Prompt $prompt): Prompt
    {
        $prompt->setPositive($this->sanitizePrompt($prompt->getPositive()));
        $prompt->setNegative($this->sanitizePrompt($prompt->getNegative()));

        return $prompt;
    }

    private function sanitizePrompt(string $prompt): string
    {
        $wordsToRemove = [
            'a',
            'an',
            'the'
        ];

        $prompt = strtolower($prompt);
        $prompt = preg_replace('/(\ba\b|\ban\b) (\w+)/i', '$1 $2,', $prompt);

        foreach ($wordsToRemove as $word) {
            $pattern = '/\b' . preg_quote($word, '/') . '\b/';
            $prompt = preg_replace($pattern, '', $prompt);
        }

        $prompt = preg_replace('/\s+/', ' ', $prompt);
        $prompt = str_replace(', ', ',', $prompt);

        return trim($prompt);
    }
}
