<?php

namespace Basilicom\AiImageGeneratorBundle\Strategy\Prompting;

use Basilicom\AiImageGeneratorBundle\Config\Model\Prompting\OpenAIPromptConfig;
use Basilicom\AiImageGeneratorBundle\Model\Prompt;
use Basilicom\AiImageGeneratorBundle\Model\ServiceRequest;
use Exception;
use Pimcore\Cache;
use Symfony\Component\HttpFoundation\Request;

class OpenAiStrategy extends Strategy
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
            $enhancedPrompt = json_decode($responseData['choices'][0]['message']['content'], true)['prompt'];
            $prompt->setPositive($enhancedPrompt);
        } catch (Exception) {
            $prompt = parent::enhancePrompt($prompt);
        }

        Cache::save($prompt, $cacheKey);

        return $prompt;
    }

    protected function getRequest(Prompt $prompt): ServiceRequest
    {
        /** @var OpenAIPromptConfig $config */
        $config = $this->promptEnhancementConfig;

        $uri = $config->getBaseUrl() . '/chat/completions';
        $method = Request::METHOD_POST;
        $payload = [
            'model' => $config->getModel(),
            'temperature' => 0.3,
            'n' => 1,
            'messages' => [
                [
                    'role' => 'system',
                    'content' =>
                        'You are a simple client that returns plain text. ' .
                        'Do not reply using a complete sentence, and only give the answer in the following format: {"prompt": "xxxxxx"}.' .
                        'Use english as language.'
                ],
                [
                    'role' => 'user',
                    'content' =>
                        'I want to create marketing images. A prompt should describe what I want to see but also some keywords to enhance the prompt. Come up with own, optimized enhancements you would do to get better, realistic image results.' .
                        'Enhance and extend the following prompt: `' . $prompt->getPositive() . '`.' .
                        'Also describe a matching background in your prompt version.'
                ],
            ],
        ];

        return new ServiceRequest($uri, $method, $payload, ['Authorization' => 'Bearer ' . $config->getApiKey()]);
    }
}
