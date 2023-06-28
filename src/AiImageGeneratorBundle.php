<?php

namespace Basilicom\AiImageGeneratorBundle;

use Exception;
use Pimcore\Extension\Bundle\AbstractPimcoreBundle;
use Pimcore\Extension\Bundle\Installer\InstallerInterface;
use Pimcore\Extension\Bundle\Traits\PackageVersionTrait;

class AiImageGeneratorBundle extends AbstractPimcoreBundle
{
    use PackageVersionTrait {
        getVersion as protected getComposerVersion;
    }

    protected function getComposerPackageName(): string
    {
        return 'basilicom/ai-image-generator';
    }

    public function getPath(): string
    {
        return \dirname(__DIR__);
    }

    public function getInstaller(): ?InstallerInterface
    {
        /** @var Installer $installer */
        $installer = $this->container->get(Installer::class);

        return $installer;
    }

    public function getVersion(): string
    {
        try {
            return $this->getComposerVersion();
        } catch (Exception) {
            return 'unknown';
        }
    }
}
