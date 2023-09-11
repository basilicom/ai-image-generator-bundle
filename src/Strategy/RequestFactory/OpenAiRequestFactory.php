<?php

namespace Basilicom\AiImageGeneratorBundle\Strategy\RequestFactory;

use Basilicom\AiImageGeneratorBundle\Config\ServiceConfiguration;
use Basilicom\AiImageGeneratorBundle\Config\Model\OpenAiApiConfig;
use Basilicom\AiImageGeneratorBundle\Model\AiImage;
use Basilicom\AiImageGeneratorBundle\Model\ServiceRequest;
use Basilicom\AiImageGeneratorBundle\Strategy\NotSupportedException;
use Basilicom\AiImageGeneratorBundle\Strategy\RequestFactory;
use Symfony\Component\HttpFoundation\Request;

class OpenAiRequestFactory implements RequestFactory
{
    public function createTxt2ImgRequest(ServiceConfiguration|OpenAiApiConfig $configuration): ServiceRequest
    {
        $uri = rtrim($configuration->getBaseUrl(), '/') . '/images/generations';
        $method = Request::METHOD_POST;

        $payload = [
            'prompt' => implode(',', $configuration->getPromptParts()),
            'size' => '1024x1024',
            'n' => 1,
            'response_format' => 'b64_json',
        ];

        return new ServiceRequest($uri, $method, $payload, ['Authorization' => 'Bearer ' . $configuration->getApiKey()]);
    }

    public function createImgVariationsRequest(
        ServiceConfiguration|OpenAiApiConfig $configuration,
        AiImage                              $baseImage
    ): ServiceRequest {
        $uri = rtrim($configuration->getBaseUrl(), '/') . '/images/variations';
        $method = Request::METHOD_POST;

        $tmpFilePath = sys_get_temp_dir() . '/ai-image-generator--open-ai.png';
        file_put_contents($tmpFilePath, $baseImage->getData(true));

        $payload = [
            [
                'name' => 'image',
                'contents' => fopen($tmpFilePath, 'rb'),
            ],
            [
                'name' => 'size',
                'contents' => '1024x1024',
            ],
            [
                'name' => 'n',
                'contents' => 1,
            ],
            [
                'name' => 'response_format',
                'contents' => 'b64_json',
            ],
        ];

        unlink($tmpFilePath);

        return new ServiceRequest($uri, $method, $payload, ['Authorization' => 'Bearer ' . $configuration->getApiKey()], true);
    }

    /**
     * @throws NotSupportedException
     */
    public function createUpscaleRequest(
        ServiceConfiguration|OpenAiApiConfig $configuration,
        AiImage                              $baseImage
    ): ServiceRequest {
        throw new NotSupportedException('Upscaling is currently not supported');
    }

    /**
     * @throws NotSupportedException
     */
    public function createInpaintBackgroundRequest(
        ServiceConfiguration|OpenAiApiConfig $configuration,
        AiImage                              $baseImage
    ): ServiceRequest {
        throw new NotSupportedException('Upscaling is currently not supported');
    }
}
