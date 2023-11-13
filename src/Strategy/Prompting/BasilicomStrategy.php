<?php

namespace Basilicom\AiImageGeneratorBundle\Strategy\Prompting;

use Basilicom\AiImageGeneratorBundle\Config\Model\Prompting\OllamaPromptConfig;
use Basilicom\AiImageGeneratorBundle\Model\Prompt;
use Basilicom\AiImageGeneratorBundle\Model\ServiceRequest;
use Exception;
use Pimcore\Cache;
use Symfony\Component\HttpFoundation\Request;

class BasilicomStrategy extends Strategy
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
            $prompt->setPositive($responseData['response']);
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

        $uri = $config->getBaseUrl() . '/generate';
        $method = Request::METHOD_POST;
        $payload = [
            'prompt' => $this->createPrompt($prompt),
        ];

        return new ServiceRequest($uri, $method, $payload);
    }

    protected function createPrompt(Prompt $prompt): string
    {
        return $prompt->getPositive() . ',';
    }
}
