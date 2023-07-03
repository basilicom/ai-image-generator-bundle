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
            BundleManagerEvents::JS_PATHS => 'onJsPaths',
            BundleManagerEvents::CSS_PATHS => 'onCssPaths',

            BundleManagerEvents::EDITMODE_JS_PATHS => 'onEditmodeJsPaths',
            BundleManagerEvents::EDITMODE_CSS_PATHS => 'onEditmodeCssPaths',
        ];
    }

    public function onJsPaths(PathsEvent $event): void
    {
        $event->setPaths(
            array_merge(
                $event->getPaths(),
                [
                    '/bundles/aiimagegenerator/runtime.js',
                    '/bundles/aiimagegenerator/backend.js'
                ]
            )
        );
    }

    public function onCssPaths(PathsEvent $event): void
    {
        $event->setPaths(
            array_merge(
                $event->getPaths(),
                [
                    '/bundles/aiimagegenerator/backend.css'
                ]
            )
        );
    }

    public function onEditmodeJsPaths(PathsEvent $event): void
    {
        $event->setPaths(
            array_merge(
                $event->getPaths(),
                [
                    '/bundles/aiimagegenerator/runtime.js',
                    '/bundles/aiimagegenerator/editmode.js'
                ]
            )
        );
    }

    public function onEditmodeCssPaths(PathsEvent $event): void
    {
        $event->setPaths(
            array_merge(
                $event->getPaths(),
                [
                    '/bundles/aiimagegenerator/editmode.css'
                ]
            )
        );
    }
}
