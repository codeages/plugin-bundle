<?php

namespace Codeages\PluginBundle\Twig;

use Codeages\Biz\Framework\DataStructure\UniquePriorityQueue;

use Twig\Extension\AbstractExtension;

class HtmlExtension extends AbstractExtension
{
    protected $scripts = array();

    protected $csses = array();

    public function __construct()
    {
        $this->scripts = new UniquePriorityQueue();
        $this->csses = new UniquePriorityQueue();
    }

    public function getFunctions()
    {
        $options = array('is_safe' => array('html'));

        return array(
            new \Twig\TwigFunction('script', array($this, 'script')),
            new \Twig\TwigFunction('css', array($this, 'css')),
        );
    }

    public function script($paths = null, $priority = 0)
    {
        if (empty($paths)) {
            return $this->scripts;
        }

        if (!is_array($paths)) {
            $paths = array($paths);
        }

        foreach ($paths as $path) {
            $this->scripts->insert($path, $priority);
        }
    }

    public function css($paths = null, $priority = 0)
    {
        if (empty($paths)) {
            return $this->csses;
        }

        if (!is_array($paths)) {
            $paths = array($paths);
        }

        foreach ($paths as $path) {
            $this->csses->insert($path, $priority);
        }
    }

    public function getName()
    {
        return 'codeages_plugin_html_extension';
    }
}
