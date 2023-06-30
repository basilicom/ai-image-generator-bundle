<?php

namespace Basilicom\AiImageGeneratorBundle\EventSubscriber;

use Pimcore\Event\BundleManager\PathsEvent;
use Pimcore\Event\BundleManagerEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class PimcoreAdminSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            BundleManagerEvents::JS_PATHS  => 'onJsPaths',
            BundleManagerEvents::CSS_PATHS => 'onCssPaths',

            BundleManagerEvents::EDITMODE_JS_PATHS  => 'onJsPaths',
            BundleManagerEvents::EDITMODE_CSS_PATHS => 'onCssPaths',
        ];
    }

    public function onJsPaths(PathsEvent $event): void
    {
        $event->setPaths(
            array_merge(
                $event->getPaths(),
                ['/bundles/aiimagegenerator/js/editable/image.js']
            )
        );
    }

    public function onCssPaths(PathsEvent $event): void
    {
        $event->setPaths(
            array_merge(
                $event->getPaths(),
                ['/bundles/aiimagegenerator/css/editable/image.css']
            )
        );
    }
}
