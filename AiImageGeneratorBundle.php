<?php

namespace Basilicom\AiImageGeneratorBundle;

use Pimcore\Extension\Bundle\AbstractPimcoreBundle;

class AiImageGeneratorBundle extends AbstractPimcoreBundle
{
    public function getInstaller(): Installer
    {
        /** @var Installer $installer */
        $installer = $this->container->get(Installer::class);

        return $installer;
    }
}
