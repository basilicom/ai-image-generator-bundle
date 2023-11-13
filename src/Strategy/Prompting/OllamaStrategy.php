<?php

namespace Basilicom\AiImageGeneratorBundle\Strategy\Prompting;

use Basilicom\AiImageGeneratorBundle\Config\Model\Prompting\OllamaPromptConfig;
use Basilicom\AiImageGeneratorBundle\Model\Prompt;
use Basilicom\AiImageGeneratorBundle\Model\ServiceRequest;
use Exception;
use Pimcore\Cache;
use Symfony\Component\HttpFoundation\Request;

class OllamaStrategy extends Strategy
{
    public function enhancePrompt(Prompt $prompt): Prompt
    {
        $cacheKey = 'prompt-' . md5($prompt->getPositive());
        $storedPrompt = Cache::load($cacheKey);
        if ($storedPrompt instanceof Prompt) {
            return $storedPrompt;
        }

        try {
            $request = $this->getRequest($prompt);
            $response = $this->requestService->callApi($request);
            $responseData = json_decode($response->getBody()->getContents(), true);
            $enhancedPrompt = json_decode($responseData['response'], true)['prompt'];
            $prompt->setPositive($enhancedPrompt);
        } catch (Exception) {
            $prompt = parent::enhancePrompt($prompt);
        }

        Cache::save($prompt, $cacheKey);

        return $prompt;
    }

    protected function getRequest(Prompt $prompt): ServiceRequest
    {
        /** @var OllamaPromptConfig $config */
        $config = $this->promptEnhancementConfig;

        $uri = $config->getBaseUrl() . '/api/generate';
        $method = Request::METHOD_POST;
        $payload = [
            'model' => $config->getModel(),
            'prompt' => $this->createPrompt($prompt),
            'options' => ['temperature' => 0.5],
            'stream' => false
        ];

        return new ServiceRequest($uri, $method, $payload);
    }

    protected function createPrompt(Prompt $prompt): string
    {
        return sprintf(
            'Do not reply using a complete sentence, and only give the answer in the following format: {"prompt": "xxxxxx"}. ' .
            'Use english as language. I want to create marketing images. Also describe a matching background in your prompt version.' .
            'Enhance and extend the following prompt to create stunning, realistic images: `%s`.',
            $prompt->getPositive()
        );
    }
}
