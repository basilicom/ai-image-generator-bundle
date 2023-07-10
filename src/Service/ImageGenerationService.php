<?php

namespace Basilicom\AiImageGeneratorBundle\Service;

use Basilicom\AiImageGeneratorBundle\Config\Configuration;
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
use Pimcore\Model\Document\PageSnippet;
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
        $config = $this->getTxt2ImageApiConfig($request);
        if ($config instanceof StableDiffusionApiConfig) {
            $this->strategy = $this->stableDiffusionStrategy;
        } elseif ($config instanceof DreamStudioApiConfig) {
            $this->strategy = $this->dreamStudioStrategy;
        }

        $aiImage = $this->strategy->textToImage($config);
        $asset = $this->createPimcoreAsset($aiImage, 'generated via ' . $config->getName());

        if ($config->isUpscale()) {
            $upscaledAiImage = $this->strategy->upscale($config, $aiImage);

            return $this->updatePimcoreAsset($asset, $upscaledAiImage, 'upscaled via ' . $config->getName());
        }

        return $asset;
    }

    private function getTxt2ImageApiConfig(Request $request): Configuration
    {
        $config = $this->configurationService->getConfiguration();
        $context = (string)$request->get('context');
        $contextElementId = (int)$request->get('id');
        $width = (int)$request->get('width');
        $height = (int)$request->get('height');

        $element = match ($context) {
            'document' => PageSnippet::getById($contextElementId),
            'object' => DataObject::getById($contextElementId),
        };

        $prompt = $this->promptCreator->createPromptParts($element);
        $negativePrompt = ['(semi-realistic, cgi, 3d, render, sketch, cartoon, drawing, anime:1.4), text, close up, cropped, out of frame, worst quality, low quality, jpeg artifacts, ugly, duplicate, morbid, mutilated, extra fingers, mutated hands, poorly drawn hands, poorly drawn face, mutation, deformed, blurry, dehydrated, bad anatomy, bad proportions, extra limbs, cloned face, disfigured, gross proportions, malformed limbs, missing arms, missing legs, extra arms, extra legs, fused fingers, too many fingers, long neck']; // todo

        $aspectRatio = $this->aspectRatioCalculator->getAspectRatioFromDimensions($width, $height);

        $config->setPromptParts($prompt);
        $config->setNegativePromptParts($negativePrompt);
        $config->setAspectRatio($aspectRatio);

        if ($width > 512 || $height > 512) {
            $config->setUpscale(true);
        }

        return $config;
    }

    /**
     * @throws Exception
     */
    public function upscaleImage(int $assetId): Asset
    {
        $config = $this->configurationService->getConfiguration();
        $config->setUpscale(true);

        if ($config instanceof StableDiffusionApiConfig) {
            $this->strategy = $this->stableDiffusionStrategy;
        } elseif ($config instanceof DreamStudioApiConfig) {
            $this->strategy = $this->dreamStudioStrategy;
        }

        $asset = Asset::getById($assetId);

        $aiImage = new AiImage();
        $aiImage->setData(base64_encode($asset->getData()));
        $upscaledAiImage = $this->strategy->upscale($config, $aiImage);

        return $this->updatePimcoreAsset($asset, $upscaledAiImage, 'upscaled via ' . $config->getName());
    }

    /**
     * @throws Exception
     */
    private function createPimcoreAsset(AiImage $generatedImage, string $versionNote = ''): Asset
    {
        $asset = new Asset();
        $asset->setParent(Asset\Service::createFolderByPath('/ai-images'));
        $asset->setKey(uniqid('ai-image') . '.png');
        $asset->setType('image');
        $asset->setData($generatedImage->getData(true));

        foreach ($generatedImage->getAllMetadata() as $key => $value) {
            $asset->addMetadata($key, 'input', $value);
        }

        return $asset->save(['versionNote' => $versionNote]);
    }

    /**
     * @throws Exception
     */
    private function updatePimcoreAsset(Asset $asset, AiImage $generatedImage, string $versionNote = ''): Asset
    {
        $asset->setData($generatedImage->getData(true));

        foreach ($generatedImage->getAllMetadata() as $key => $value) {
            $asset->addMetadata($key, 'input', $value);
        }

        return $asset->save(['versionNote' => $versionNote]);
    }
}
