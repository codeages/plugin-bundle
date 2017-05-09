<?php
namespace Codeages\PluginBundle\Twig;

class DictExtension extends \Twig_Extension
{
    protected $collector;
    protected $locale;

    public function __construct($collector,$container)
    {
        $this->collector = $collector;
        $this->locale = $container->get('request')->getLocale();
    }

    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('dict', array($this, 'getDict')),
            new \Twig_SimpleFunction('dict_text', array($this, 'getDictText'), array('is_safe' => array('html'))),
        );
    }

    public function getDict($name)
    {
        return $this->collector->getDictMap($this->locale,$name);
    }

    public function getDictText($name, $key, $default = '')
    {
        return $this->collector->getDictText($this->locale,$name, $key, $default);
    }

    public function getName()
    {
        return 'codeages_plugin_dict_extension';
    }
}
