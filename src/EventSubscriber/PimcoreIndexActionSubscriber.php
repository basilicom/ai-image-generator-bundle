<?php

namespace Basilicom\AiImageGeneratorBundle\EventSubscriber;

use Basilicom\AiImageGeneratorBundle\Config\ConfigurationService;
use Basilicom\AiImageGeneratorBundle\Model\FeatureEnum;
use Pimcore\Bundle\AdminBundle\Event\AdminEvents;
use Pimcore\Bundle\AdminBundle\Event\IndexActionSettingsEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class PimcoreIndexActionSubscriber implements EventSubscriberInterface
{
    private ConfigurationService $configurationService;

    public function __construct(ConfigurationService $configurationService)
    {
        $this->configurationService = $configurationService;
    }

    public static function getSubscribedEvents(): array
    {
        return [AdminEvents::INDEX_ACTION_SETTINGS => 'setBundleSettings'];
    }

    public function setBundleSettings(IndexActionSettingsEvent $event): void
    {
        $event->addSetting(
            'AiImageGeneratorBundle',
            [
                'adapter' => [
                    FeatureEnum::TXT_2_IMG => $this->configurationService->getServiceConfiguration(FeatureEnum::TXT_2_IMG)->getName(),
                    FeatureEnum::UPSCALE => $this->configurationService->getServiceConfiguration(FeatureEnum::UPSCALE)->getName(),
                    FeatureEnum::IMAGE_VARIATIONS => $this->configurationService->getServiceConfiguration(FeatureEnum::IMAGE_VARIATIONS)->getName(),
                    FeatureEnum::INPAINT => $this->configurationService->getServiceConfiguration(FeatureEnum::INPAINT)->getName(),
                    FeatureEnum::INPAINT_BACKGROUND => $this->configurationService->getServiceConfiguration(FeatureEnum::INPAINT_BACKGROUND)->getName(),
                ]
            ]
        );
    }
}
