<?php

namespace Basilicom\AiImageGeneratorBundle\Service;

use Basilicom\AiImageGeneratorBundle\Config\Model\ImageGenerationConfig;
use Basilicom\AiImageGeneratorBundle\Config\Model\ImageGeneration\ClipDropApiConfig;
use Basilicom\AiImageGeneratorBundle\Config\Model\ImageGeneration\DreamStudioApiConfig;
use Basilicom\AiImageGeneratorBundle\Config\Model\ImageGeneration\OpenAiApiConfig;
use Basilicom\AiImageGeneratorBundle\Config\Model\ImageGeneration\StableDiffusionApiConfig;
use Basilicom\AiImageGeneratorBundle\Model\AiImage;
use Basilicom\AiImageGeneratorBundle\Model\MetaDataEnum;
use Basilicom\AiImageGeneratorBundle\Strategy\ImageGeneration\ClipDropStrategy;
use Basilicom\AiImageGeneratorBundle\Strategy\ImageGeneration\DreamStudioStrategy;
use Basilicom\AiImageGeneratorBundle\Strategy\ImageGeneration\OpenAiStrategy;
use Basilicom\AiImageGeneratorBundle\Strategy\ImageGeneration\StableDiffusionStrategy;
use Basilicom\AiImageGeneratorBundle\Strategy\ImageGeneration\Strategy;
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

    private function setStrategy(ImageGenerationConfig $config): void
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
    public function generateImage(ImageGenerationConfig $config): Asset
    {
        $this->setStrategy($config);

        $aiImage = $this->strategy->textToImage($config);
        $asset = $this->createPimcoreAsset($aiImage, 'generated via ' . $config->getName());

        return $this->upscaleIfPossible($asset, $aiImage, $config);
    }

    /**
     * @throws Exception
     */
    public function varyImage(ImageGenerationConfig $config, Asset\Image $asset): Asset\Image
    {
        $this->setStrategy($config);

        $aiImage = $this->strategy->imageVariations($config, AiImage::fromAsset($asset));

        return $this->updatePimcoreAsset($asset, $aiImage, 'created variation via ' . $config->getName());
    }

    /**
     * @throws Exception
     */
    public function upscaleImage(ImageGenerationConfig $config, int $assetId): Asset\Image
    {
        $this->setStrategy($config);

        $asset = Asset\Image::getById($assetId);
        if ($prompt = $asset->getMetadata(MetaDataEnum::PROMPT)) {
            $config->setPrompt($prompt);
        }
        if ($negativePrompt = $asset->getMetadata(MetaDataEnum::NEGATIVE_PROMPT)) {
            $config->setPrompt($negativePrompt);
        }

        $aiImage = AiImage::fromAsset($asset);

        return $this->upscaleIfPossible($asset, $aiImage, $config);
    }

    /**
     * @throws Exception
     */
    public function inpaintBackground(ImageGenerationConfig $config, int $assetId): Asset\Image
    {
        $this->setStrategy($config);

        $asset = Asset\Image::getById($assetId);

        $versions = $asset->getVersions();
        $firstVersion = $versions[0]->getData();

        $aiImage = $this->strategy->inpaintBackground($config, AiImage::fromAsset($firstVersion, true));
        $asset = $this->updatePimcoreAsset($asset, $aiImage, 'created background inpaint via ' . $config->getName());

        return $this->upscaleIfPossible($asset, $aiImage, $config);
    }

    /**
     * @throws Exception
     */
    public function inpaint(ImageGenerationConfig $config, int $assetId, bool $save = true): Asset\Image
    {
        $this->setStrategy($config);

        $asset = Asset\Image::getById($assetId);
        $aiImage = $this->strategy->inpaint($config, AiImage::fromAsset($asset, true));

        return $this->updatePimcoreAsset($asset, $aiImage, 'created inpaint via ' . $config->getName(), $save);
    }

    private function upscaleIfPossible(Asset\Image $asset, AiImage $aiImage, ImageGenerationConfig $config): Asset\Image
    {
        return $asset;
        if ($asset->getWidth() < 4096 && $asset->getHeight() < 4096) {
            try {
                $asset = $this->updatePimcoreAsset(
                    $asset,
                    $this->strategy->upscale($config, $aiImage),
                    'upscaled via ' . $config->getName()
                );
            } catch (Exception) {
            }
        }

        return $asset;
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
    private function updatePimcoreAsset(
        Asset\Image $asset,
        AiImage $generatedImage,
        string $versionNote = '',
        bool $save = true
    ): Asset\Image {
        $asset->setData($generatedImage->getData(true));

        foreach ($generatedImage->getAllMetadata() as $key => $value) {
            $asset->addMetadata($key, 'input', $value);
        }

        return $save ? $asset->save(['versionNote' => $versionNote]) : $asset;
    }
}
