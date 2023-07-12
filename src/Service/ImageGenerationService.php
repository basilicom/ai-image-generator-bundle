<?php

namespace Basilicom\AiImageGeneratorBundle\Service;

use Basilicom\AiImageGeneratorBundle\Config\Configuration;
use Basilicom\AiImageGeneratorBundle\Config\Model\DreamStudioApiConfig;
use Basilicom\AiImageGeneratorBundle\Config\Model\StableDiffusionApiConfig;
use Basilicom\AiImageGeneratorBundle\Model\AiImage;
use Basilicom\AiImageGeneratorBundle\Model\MetaDataEnum;
use Basilicom\AiImageGeneratorBundle\Strategy\DreamStudioStrategy;
use Basilicom\AiImageGeneratorBundle\Strategy\StableDiffusionStrategy;
use Basilicom\AiImageGeneratorBundle\Strategy\Strategy;
use Exception;
use Pimcore\Model\Asset;

class ImageGenerationService
{
    private Strategy $strategy;
    private StableDiffusionStrategy $stableDiffusionStrategy;
    private DreamStudioStrategy $dreamStudioStrategy;

    public function __construct(
        StableDiffusionStrategy $stableDiffusionStrategy,
        DreamStudioStrategy     $dreamStudioStrategy
    ) {
        $this->stableDiffusionStrategy = $stableDiffusionStrategy;
        $this->dreamStudioStrategy = $dreamStudioStrategy;
    }

    /**
     * @throws Exception
     */
    public function generateImage(Configuration $config): Asset
    {
        $this->setStrategy($config);

        $aiImage = $this->strategy->textToImage($config);
        $asset = $this->createPimcoreAsset($aiImage, 'generated via ' . $config->getName());

        if ($config->isUpscale()) {
            $upscaledAiImage = $this->strategy->upscale($config, $aiImage);

            return $this->updatePimcoreAsset($asset, $upscaledAiImage, 'upscaled via ' . $config->getName());
        }

        return $asset;
    }

    /**
     * @throws Exception
     */
    public function varyImage(Configuration $config, Asset $asset): Asset
    {
        $this->setStrategy($config);

        $aiImage = $this->strategy->textToImage($config);

        return $this->updatePimcoreAsset($asset, $aiImage, 'created variation via ' . $config->getName());
    }

    /**
     * @throws Exception
     */
    public function upscaleImage(Configuration $config, int $assetId): Asset
    {
        $this->setStrategy($config);

        $asset = Asset::getById($assetId);
        if ($prompt = $asset->getMetadata(MetaDataEnum::PROMPT)) {
            $config->setPromptParts([$prompt]);
        }
        if ($negativePrompt = $asset->getMetadata(MetaDataEnum::NEGATIVE_PROMPT)) {
            $config->setPromptParts([$negativePrompt]);
        }

        $aiImage = new AiImage();
        $aiImage->setData(base64_encode($asset->getData()));
        $upscaledAiImage = $this->strategy->upscale($config, $aiImage);

        return $this->updatePimcoreAsset($asset, $upscaledAiImage, 'upscaled via ' . $config->getName());
    }

    private function setStrategy(Configuration $config): void
    {
        if ($config instanceof StableDiffusionApiConfig) {
            $this->strategy = $this->stableDiffusionStrategy;
        } elseif ($config instanceof DreamStudioApiConfig) {
            $this->strategy = $this->dreamStudioStrategy;
        }
    }

    /**
     * @throws Exception
     */
    private function createPimcoreAsset(AiImage $generatedImage, string $versionNote = ''): Asset
    {
        $asset = new Asset();
        $asset->setParent(Asset\Service::createFolderByPath('/ai-images'));
        $asset->setKey(uniqid('ai-image-') . '.png');
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
