<?php

namespace Basilicom\AiImageGeneratorBundle\EventSubscriber;

use Basilicom\AiImageGeneratorBundle\Config\ConfigurationService;
use Pimcore\Bundle\AdminBundle\Event\AdminEvents;
use Pimcore\Bundle\AdminBundle\Event\IndexActionSettingsEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class PimcoreIndexActionSubscriber implements EventSubscriberInterface
{
    public function __construct(private ConfigurationService $configurationService)
    {
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
                'adapter' => $this->configurationService->getConfiguration()->getName()
            ]
        );
    }
}
