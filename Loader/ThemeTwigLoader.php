<?php

namespace Codeages\PluginBundle\Loader;

use Codeages\PluginBundle\System\PluginableHttpKernelInterface;

class ThemeTwigLoader extends \Twig_Loader_Filesystem
{
    /**
     * @var PluginableHttpKernelInterface
     */
    private $kernel;

    public function __construct(PluginableHttpKernelInterface $kernel)
    {
        $this->kernel = $kernel;
        parent::__construct(array());
    }

    public function findTemplate($name, $throw = true)
    {
        $logicalName = (string)$name;

        if (isset($this->cache[$logicalName])) {
            return $this->cache[$logicalName];
        }

        $previous = null;
        $file     = $this->getCustomFile($logicalName);

        if (is_null($file)) {
            $file = $this->getThemeFile($logicalName);
        }

        if ($file === false || null === $file) {
            throw new \Twig_Error_Loader(sprintf('Unable to find template "%s".', $logicalName));
        }

        return $this->cache[$logicalName] = $file;
    }

    protected function getThemeFile($file)
    {

        if ($this->isAppResourceFile($file)) {
            $themeDir = $this->kernel->getPluginConfigurationManager()->getActiveThemeDirectory();
            $file     = $themeDir . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . $file;
        }

        if (is_file($file)) {
            return $file;
        }

        return null;
    }

    protected function getCustomFile($file)
    {
        if ($this->isAppResourceFile($file)) {
            return $this->kernel->getRootDir() . DIRECTORY_SEPARATOR .'..' . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'Custom' . DIRECTORY_SEPARATOR . 'Resources' . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . $file;
        }

        if (is_file($file)) {
            return $file;
        }

        return null;
    }

    protected function isAppResourceFile($file)
    {
        return strpos((string)$file, 'Bundle') === false && strpos((string)$file, '@') !== 0;
    }
}