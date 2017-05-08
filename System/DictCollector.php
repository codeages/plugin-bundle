<?php

namespace Codeages\PluginBundle\System;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Config\ConfigCache;
use Symfony\Component\Config\Resource\FileResource;

class DictCollector
{
    protected $dict = array();
    protected $locale ;
    protected $locale_fallback;
    protected $cacheDir;
    protected $debug;
    protected $files;

    public function __construct(array $files, $cacheDir, $debug,$locale,$locale_fallback)
    {
        $this->files = $files;
        $this->locale = $locale;
        $this->locale_fallback = $locale_fallback;
        $this->cacheDir = $cacheDir;
        $this->debug = $debug;


        $cacheFile = $cacheDir . "/dict.{$locale}.php";
        $cache = new ConfigCache($cacheFile, $debug);
        if ($cache->isFresh() === false) {
            $this->loadDictFile();
        } else {
            $this->dict = require $cacheFile;
        }
    }


    private function  loadDictFile(){
        $resources = array();
        $dict = array();
        $defaultDict = array();

        foreach ($this->files as $file) {
            $fileParts = explode('.',$file);
            $locale = $fileParts[1];
            $resources[$locale][] = new FileResource($file);

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
                $localDict = array_replace_recursive($defaultDict,$localDict);
            }
            $cache->write(sprintf('<?php return %s;', var_export($localDict, true)), $resources[$key]);
            if($this->locale == $key){
                $this->dict = $dict;
            }
        }
    }

    public function getDictText($name, $key, $default = '')
    {
        if (!isset($this->dict[$name][$key])) {
            return $default;
        }

        return (string)($this->dict[$name][$key]);
    }

    public function getDictMap($name)
    {
        if (!isset($this->dict[$name])) {
            return array();
        }

        return (array)($this->dict[$name]);
    }

}