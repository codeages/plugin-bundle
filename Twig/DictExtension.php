<?php

namespace Codeages\PluginBundle\Twig;

use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Extension\AbstractExtension;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Codeages\PluginBundle\System\DictCollector;

class DictExtension extends AbstractExtension
{
    protected $collector;
    protected $locale;
    protected $container;
    protected $requestStack;

    public function __construct(DictCollector $collector, ContainerInterface $container, RequestStack $requestStack)
    {
        $this->collector = $collector;
        $this->container = $container;
        $this->requestStack = $requestStack;
    }

    public function getFunctions()
    {
        return array(
            new \Twig\TwigFunction('dict', array($this, 'getDict')),
            new \Twig\TwigFunction('dict_text', array($this, 'getDictText'), array('is_safe' => array('html'))),
        );
    }

    public function getDict($name)
    {
        $locale = $this->getLocale();

        return $this->collector->getDictMap($locale, $name);
    }

    public function getDictText($name, $key, $default = '')
    {
        $locale = $this->getLocale();

        return $this->collector->getDictText($locale, $name, $key, $default);
    }

    public function getName()
    {
        return 'codeages_plugin_dict_extension';
    }

    private function getLocale()
    {
        if (!$this->locale) {
            $this->locale = $this->requestStack->getMainRequest()->getLocale();
        }

        return $this->locale;
    }
}
