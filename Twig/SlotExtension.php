<?php

namespace Codeages\PluginBundle\Twig;

use Twig\Extension\AbstractExtension;

class SlotExtension extends AbstractExtension
{
    protected $manager;

    public function __construct($manager)
    {
        $this->manager = $manager;
    }

    public function getFunctions()
    {
        return array(
            new \Twig\TwigFunction('slot', array($this, 'slot'), array('is_safe' => array('html'))),
        );
    }

    public function slot($name, $args = array())
    {
        return $this->manager->fire($name, $args);
    }

    public function getName()
    {
        return 'codeages_plugin_slot_extension';
    }
}
