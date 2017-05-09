<?php

namespace Codeages\PluginBundle\System;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Config\ConfigCache;
use Symfony\Component\Config\Resource\FileResource;

class DictCollector
{
    protected $dict = array();
    protected $locale_fallback;
    protected $cacheDir;
    protected $debug;
    protected $files;

    public function __construct(array $files, $cacheDir, $debug,$locale_fallback)
    {
        $this->files = $files;
        $this->cacheDir = $cacheDir;
        $this->debug = $debug;
        $this->locale_fallback = $locale_fallback;
    }

    private function  loadDictFile(){
        $resources = array();
        $dict = array();
        $defaultDict = array();

        foreach ($this->files as $file) {
            $fileParts = explode('.',$file);
            $locale = $fileParts[1];
            $resources[] = new FileResource($file);

            $localeDict = isset($dict[$locale]) ? $dict[$locale] : array();
            $dict[$locale] = array_merge($localeDict,Yaml::parse(file_get_contents($file)));

            if($locale == $this->locale_fallback){
                $defaultDict = array_merge($defaultDict,Yaml::parse(file_get_contents($file)));
            }
        }

        $this->cacheDictFile($dict,$defaultDict,$resources);
    }

    private function  cacheDictFile($dict,$defaultDict,$resources){
        foreach ($dict as $key => $localDict){
            $cacheFile = $this->cacheDir . "/dict.{$key}.php";
            $cache = new ConfigCache($cacheFile, $this->debug);
            if($key != $this->locale_fallback){
                $localDict = array_merge($defaultDict,$localDict);
            }
            $cache->write(sprintf('<?php return %s;', var_export($localDict, true)), $resources);
        }
    }

    private function  getDict($userLocale){
        $userLocaleCacheFile = $this->cacheDir . "/dict.{$userLocale}.php";
        $cache = new ConfigCache(locale_fallback, $this->debug);
        if ($cache->isFresh() === false) {
             $this->loadDictFile();
        }
        $this->dict = require $userLocaleCacheFile;
    }

    public function getDictText($userLocale,$name, $key, $default = '')
    {
        $this->dict = $this->getDict($userLocale);
        if (!isset($this->dict[$name][$key])) {
            return $default;
        }

        return (string)($this->dict[$name][$key]);
    }

    public function getDictMap($userLocale,$name)
    {
        $this->dict = $this->getDict($userLocale);
        if (!isset($this->dict[$name])) {
            return array();
        }

        return (array)($this->dict[$name]);
    }

}