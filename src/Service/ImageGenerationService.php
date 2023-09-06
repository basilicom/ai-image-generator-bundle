<?php

namespace Basilicom\AiImageGeneratorBundle\Service;

use Basilicom\AiImageGeneratorBundle\Config\Configuration;
use Basilicom\AiImageGeneratorBundle\Config\Model\ClipDropApiConfig;
use Basilicom\AiImageGeneratorBundle\Config\Model\DreamStudioApiConfig;
use Basilicom\AiImageGeneratorBundle\Config\Model\OpenAiApiConfig;
use Basilicom\AiImageGeneratorBundle\Config\Model\StableDiffusionApiConfig;
use Basilicom\AiImageGeneratorBundle\Model\AiImage;
use Basilicom\AiImageGeneratorBundle\Model\MetaDataEnum;
use Basilicom\AiImageGeneratorBundle\Strategy\ClipDropStrategy;
use Basilicom\AiImageGeneratorBundle\Strategy\DreamStudioStrategy;
use Basilicom\AiImageGeneratorBundle\Strategy\NotSupportedException;
use Basilicom\AiImageGeneratorBundle\Strategy\OpenAiStrategy;
use Basilicom\AiImageGeneratorBundle\Strategy\StableDiffusionStrategy;
use Basilicom\AiImageGeneratorBundle\Strategy\Strategy;
use Exception;
use Pimcore\Model\Asset;
use Psr\Container\ContainerInterface;

class ImageGenerationService
{
    private Strategy $strategy;
    private ContainerInterface $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    private function setStrategy(Configuration $config): void
    {
        if ($config instanceof StableDiffusionApiConfig) {
            $this->strategy = $this->container->get(StableDiffusionStrategy::class);
        } elseif ($config instanceof DreamStudioApiConfig) {
            $this->strategy = $this->container->get(DreamStudioStrategy::class);
        } elseif ($config instanceof OpenAiApiConfig) {
            $this->strategy = $this->container->get(OpenAiStrategy::class);
        } elseif ($config instanceof ClipDropApiConfig) {
            $this->strategy = $this->container->get(ClipDropStrategy::class);
        }
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
            try {
                $aiImage = $this->strategy->upscale($config, $aiImage);

                return $this->updatePimcoreAsset($asset, $aiImage, 'upscaled via ' . $config->getName());
            } catch (NotSupportedException) {
            }
        }

        return $asset;
    }

    /**
     * @throws Exception
     */
    public function varyImage(Configuration $config, Asset\Image $asset): Asset\Image
    {
        $this->setStrategy($config);

        $aiImage = $this->strategy->imageVariations($config, AiImage::fromAsset($asset));

        return $this->updatePimcoreAsset($asset, $aiImage, 'created variation via ' . $config->getName());
    }

    /**
     * @throws Exception
     */
    public function upscaleImage(Configuration $config, int $assetId): Asset\Image
    {
        $this->setStrategy($config);

        $asset = Asset\Image::getById($assetId);
        if ($prompt = $asset->getMetadata(MetaDataEnum::PROMPT)) {
            $config->setPromptParts([$prompt]);
        }
        if ($negativePrompt = $asset->getMetadata(MetaDataEnum::NEGATIVE_PROMPT)) {
            $config->setPromptParts([$negativePrompt]);
        }

        $upscaledAiImage = $this->strategy->upscale($config, AiImage::fromAsset($asset));

        return $this->updatePimcoreAsset($asset, $upscaledAiImage, 'upscaled via ' . $config->getName());
    }

    /**
     * @throws Exception
     */
    public function inpaintBackground(Configuration $config, int $assetId): Asset\Image
    {
        $this->setStrategy($config);

        $asset = Asset\Image::getById($assetId);

        $versions = $asset->getVersions();
        $firstVersion = $versions[0]->getData();

        $inpaintedImage = $this->strategy->inpaintBackground($config, AiImage::fromAsset($firstVersion, true));
        $inpaintedImage = $this->strategy->upscale($config, $inpaintedImage);

        return $this->updatePimcoreAsset($asset, $inpaintedImage, 'created background inpaint via ' . $config->getName());
    }

    /**
     * @throws Exception
     */
    private function createPimcoreAsset(AiImage $generatedImage, string $versionNote = ''): Asset\Image
    {
        $asset = new  Asset\Image();
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
    private function updatePimcoreAsset(Asset\Image $asset, AiImage $generatedImage, string $versionNote = ''): Asset\Image
    {
        $asset->setData($generatedImage->getData(true));

        foreach ($generatedImage->getAllMetadata() as $key => $value) {
            $asset->addMetadata($key, 'input', $value);
        }

        return $asset->save(['versionNote' => $versionNote]);
    }
}
