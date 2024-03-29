<?php

namespace Codeages\PluginBundle\DependencyInjection;

use Symfony\Bundle\FrameworkBundle\DependencyInjection\FrameworkExtension as BaseFrameworkExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\Resource\DirectoryResource;
use Symfony\Component\Finder\Finder;
use Codeages\PluginBundle\System\PluginConfigurationManager;

class FrameworkExtension extends BaseFrameworkExtension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        parent::load($configs, $container);

        $configuration = $this->getConfiguration($configs, $container);
        $config = $this->processConfiguration($configuration, $configs);

        $this->registerThemeTranslatorConfiguration($config['translator'], $container);
    }

    protected function registerThemeTranslatorConfiguration(array $config, ContainerBuilder $container)
    {
        if (!$this->isConfigEnabled($container, $config)) {
            return;
        }

        $translator = $container->findDefinition('translator.default');

        $rootDir = $container->getParameter('kernel.root_dir');

        $pluginConfigurationManager = new PluginConfigurationManager($rootDir);

        $themeDir = $pluginConfigurationManager->getActiveThemeDirectory();
        if (empty($themeDir)) {
            return;
        }

        if (!is_dir($themeDir)) {
            throw new \RuntimeException("Theme directory `{$themeDir}` is not exist.");
        }

        $container->addResource(new DirectoryResource($themeDir));

        $files = array();
        $finder = Finder::create()
            ->files()
            ->filter(function (\SplFileInfo $file) {
                return 2 === substr_count($file->getBasename(), '.') && preg_match('/\.\w+$/', $file->getBasename());
            })
            ->in($themeDir)
        ;

        foreach ($finder as $file) {
            list(, $locale) = explode('.', $file->getBasename(), 3);
            if (!isset($files[$locale])) {
                $files[$locale] = array();
            }

            $files[$locale][] = (string) $file;
        }

        $options = $translator->getArgument(4);
        
        //此处代码因教培Symfony版本(3.4)升级时, 未兼容企培版本, 此处增加兼容企培版本
        $isNewVersion = 1;
        if(empty($options)){
            $isNewVersion = 0;
            $options = $translator->getArgument(3);            
        }
        $options['resource_files'] = array_merge($options['resource_files'], $files);

        if($isNewVersion == 0){
            $translator->replaceArgument(3, $options);
        }else{
            $translator->replaceArgument(4, $options);
        }
    }
}
