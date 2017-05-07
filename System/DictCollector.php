<?php

namespace Codeages\PluginBundle\System;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Config\ConfigCache;
use Symfony\Component\Config\Resource\FileResource;

class DictCollector
{
    protected $dict = array();

    public function __construct(array $files, $cacheDir, $debug)
    {
        $cacheFile = $cacheDir . '/dict.php';
        $cache = new ConfigCache($cacheFile, $debug);

        if ($cache->isFresh() === false) {
            $resources = array();
            $dict = array();

            foreach ($files as $file) {
                $resources[] = new FileResource($file);
                $fileParts = explode('.',$file);
                $locale = $fileParts[1];
                $localeDict = isset($dict[$locale]) ? $dict[$locale] : array();
                $dict[$locale] = array_merge($localeDict,Yaml::parse(file_get_contents($file)));
            }
            $cache->write(sprintf('<?php return %s;', var_export($dict, true)), $resources);
            $this->dict = $dict;
        } else {
            $this->dict = require $cacheFile;
        }
    }

    public function getDictText($locale,$name, $key, $default = '')
    {
        if (!isset($this->dict[$locale][$name][$key])) {
            return $default;
        }

        return (string)($this->dict[$locale][$name][$key]);
    }

    public function getDictMap($locale,$name)
    {
        if (!isset($this->dict[$locale][$name])) {
            return array();
        }

        return (array)($this->dict[$locale][$name]);
    }

}