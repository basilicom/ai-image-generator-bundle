<?php

namespace Basilicom\AiImageGeneratorBundle\Config;

use Basilicom\AiImageGeneratorBundle\Config\Model\BundleConfiguration;
use Basilicom\AiImageGeneratorBundle\Config\Model\ImageGenerationConfig;
use Basilicom\AiImageGeneratorBundle\Config\Model\PromptEnhancementConfig;
use Basilicom\AiImageGeneratorBundle\Model\AiImage;
use Imagick;
use ImagickPixel;
use Pimcore\Cache;

class ConfigurationService
{
    public const CACHE_TAG = 'ai-image-bundle';

    private BundleConfiguration $config;

    public function __construct(
        ConfigurationFactory $factory,
        array                $configData
    ) {
        $this->config = $factory->createBundleConfiguration($configData);
    }

    public function getServiceConfiguration(string $feature): ?ImageGenerationConfig
    {
        $usedService = $this->config->getUsedServiceForFeature($feature);

        return $this->config->getServiceConfiguration($usedService);
    }

    public function getBrandColors(): array
    {
        return $this->config->getBrandConfiguration()->getColorScheme();
    }

    public function getBrandReferenceImage(int $width, int $height): AiImage
    {
        $colors = $this->getBrandColors();
        if (empty($colors)) {
            return new AiImage();
        }

        $cacheKey = 'aiimagebundle_brand_' . implode('_', $colors) . '_' . $width . '_' . $height;
        $cachedImage = Cache::load($cacheKey);
        if ($cachedImage) {
            return $cachedImage;
        }

        $image = $this->getGradientImage($width, $height, $colors);

        $aiImage = new AiImage();
        $aiImage->setData(base64_encode($image->getImageBlob()));

        Cache::save($aiImage, $cacheKey, [self::CACHE_TAG]);

        return $aiImage;
    }

    /**
     * todo => improve
     */
    protected function getGradientImage(int $finalWidth, int $finalHeight, array $colors): Imagick
    {
        $width = 1024;
        $height = 1024;

        $image = new Imagick();
        $image->newImage($width, $height, new ImagickPixel('none'));
        $image->setImageFormat('PNG');

        $gradientImage = new Imagick();
        $gradientImage->newImage($width, $height, new ImagickPixel('none'));
        $gradientImage->setImageFormat('PNG');

        $segmentHeight = $height / (count($colors) - 1);
        for ($i = 0; $i < count($colors) - 1; $i++) {
            $gradient = new Imagick();
            $gradient->newPseudoImage($width, $segmentHeight, 'gradient:' . $colors[$i] . '-' . $colors[$i + 1]);
            $gradientImage->compositeImage($gradient, Imagick::COMPOSITE_OVER, 0, $i * $segmentHeight);
        }

        $diagonalLength = sqrt($width * $width + $height * $height);
        $angle = atan2($width, $height) * -180 / M_PI;
        $gradientImage->rotateImage(new ImagickPixel('#fff'), $angle);
        $gradientImage->resizeImage($width * 2, $height * 2, Imagick::FILTER_LANCZOS, 1);

        $offsetX = ($diagonalLength - $width) / 2;
        $offsetY = ($diagonalLength - $height) / 2;

        $gradientImage->cropImage($width, $height, $offsetX, $offsetY);
        $image->compositeImage($gradientImage, Imagick::COMPOSITE_OVER, 0, 0);
        $image->resizeImage($finalWidth, $finalHeight, Imagick::FILTER_LANCZOS, 1);

        return $image;
    }

    public function getPromptEnhancementConfiguration(): PromptEnhancementConfig
    {
        return $this->config->getPromptEnhancementConfiguration();
    }
}
