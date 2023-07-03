<?php

namespace Basilicom\AiImageGeneratorBundle\Service;

use Basilicom\AiImageGeneratorBundle\Config\AbstractConfiguration;
use Basilicom\AiImageGeneratorBundle\Config\ConfigurationService;
use Basilicom\AiImageGeneratorBundle\Config\Model\DreamStudioApiConfig;
use Basilicom\AiImageGeneratorBundle\Config\Model\StableDiffusionApiConfig;
use Basilicom\AiImageGeneratorBundle\Helper\AspectRatioCalculator;
use Basilicom\AiImageGeneratorBundle\Model\AiImage;
use Basilicom\AiImageGeneratorBundle\Strategy\DreamStudioStrategy;
use Basilicom\AiImageGeneratorBundle\Strategy\StableDiffusionStrategy;
use Basilicom\AiImageGeneratorBundle\Strategy\Strategy;
use Exception;
use Pimcore\Model\Asset;
use Pimcore\Model\DataObject;
use Pimcore\Model\Document\Page;
use Symfony\Component\HttpFoundation\Request;

class ImageGenerationService
{
    private Strategy $strategy;
    private ConfigurationService $configurationService;
    private StableDiffusionStrategy $stableDiffusionStrategy;
    private DreamStudioStrategy $dreamStudioStrategy;
    private PromptCreator $promptCreator;
    private AspectRatioCalculator $aspectRatioCalculator;

    public function __construct(
        ConfigurationService    $configurationService,
        StableDiffusionStrategy $stableDiffusionStrategy,
        DreamStudioStrategy     $dreamStudioStrategy,
        PromptCreator           $promptCreator,
        AspectRatioCalculator   $aspectRatioCalculator
    ) {
        $this->configurationService = $configurationService;
        $this->stableDiffusionStrategy = $stableDiffusionStrategy;
        $this->dreamStudioStrategy = $dreamStudioStrategy;
        $this->promptCreator = $promptCreator;
        $this->aspectRatioCalculator = $aspectRatioCalculator;
    }

    /**
     * @throws Exception
     */
    public function generateImage(Request $request): Asset
    {
        $config = $this->getServiceRequestConfig($request);
        if ($config instanceof StableDiffusionApiConfig) {
            $this->strategy = $this->stableDiffusionStrategy;
        } elseif ($config instanceof DreamStudioApiConfig) {
            $this->strategy = $this->dreamStudioStrategy;
        }

        $this->strategy->setConfig($config);
        $aiImage = $this->strategy->requestImage();

        return $this->createPimcoreAsset($aiImage);
    }

    private function getServiceRequestConfig(Request $request): AbstractConfiguration
    {
        $config = $this->configurationService->getConfiguration();
        $context = (string)$request->get('context');
        $id = (int)$request->get('id');
        $width = (int)$request->get('width');
        $height = (int)$request->get('height');

        $element = match ($context) {
            'document' => Page::getById($id),
            'object' => DataObject::getById($id),
        };

        // todo ==> move to strategies
        $prompt = $this->promptCreator->createPromptParts($element);
        // todo
        $negativePrompt = ['(semi-realistic, cgi, 3d, render, sketch, cartoon, drawing, anime:1.4), text, close up, cropped, out of frame, worst quality, low quality, jpeg artifacts, ugly, duplicate, morbid, mutilated, extra fingers, mutated hands, poorly drawn hands, poorly drawn face, mutation, deformed, blurry, dehydrated, bad anatomy, bad proportions, extra limbs, cloned face, disfigured, gross proportions, malformed limbs, missing arms, missing legs, extra arms, extra legs, fused fingers, too many fingers, long neck'];

        $aspectRatio = $this->aspectRatioCalculator->getAspectRatioFromDimensions($width, $height);

        $config->setPromptParts($prompt);
        $config->setNegativePromptParts($negativePrompt);
        $config->setAspectRatio($aspectRatio);

        return $config;
    }

    /**
     * @throws Exception
     */
    private function createPimcoreAsset(AiImage $generatedImage): Asset
    {
        $asset = new Asset();
        $asset->setParent(Asset\Service::createFolderByPath('/ai-images'));
        $asset->setKey(uniqid('ai-image') . '.png');
        $asset->setType('image');
        $asset->setData($generatedImage->getData());

        foreach ($generatedImage->getAllMetadata() as $key => $value) {
            $asset->addMetadata($key, 'input', $value);
        }

        return $asset->save();
    }
}
