<?php

namespace Codeages\PluginBundle\System\Slot;

class SlotManager
{
    protected $dispatcher;

    protected $collector;

    protected $container;

    public function __construct($collector, $container)
    {
        $this->collector = $collector;
        $this->container = $container;
    }

    public function fire($name, $args)
    {
        $injections = $this->collector->getInjections($name);
        if (empty($injections)) {
            return '';
        }

        $contents = [];

        foreach ($injections as $name => $class) {
            $injection = new $class();
            $injection->setContainer($this->container);
            $injection->setArgements($args);

            $contents[] = $injection->inject();
        }

        return implode('', $contents);
    }
}
