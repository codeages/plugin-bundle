<?php

namespace Codeages\PluginBundle\Tests\Event\Fixture;

use Codeages\PluginBundle\FrameworkBundle;
use Codeages\PluginBundle\System\PluginableHttpKernelInterface;
use Codeages\PluginBundle\System\PluginConfigurationManager;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Kernel;

class TestKernel extends Kernel implements PluginableHttpKernelInterface
{
    public function getCacheDir(): string
    {
        return dirname(__DIR__).'/app/cache';
    }

    public function registerBundles(): iterable
    {
        $bundles = [
            new FrameworkBundle(),
        ];

        return $bundles;
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__.'/app/config/config.yml');
    }

    public function getPluginConfigurationManager()
    {
        return new PluginConfigurationManager(__DIR__);
    }
}
