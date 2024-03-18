<?php

namespace Codeages\PluginBundle\Event;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class LazyDispatcher extends EventDispatcher implements EventDispatcherInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function dispatch(object $event, ?string $eventName = null): object
    {
        $subscribers = $this->container->get('codeags_plugin.event.lazy_subscribers');

        $callbacks = $subscribers->getCallbacks($eventName);

        foreach ($callbacks as $callback) {
            if ($event->isPropagationStopped()) {
                break;
            }

            list($id, $method) = $callback;
            if ($this->container->has($id)) {
                call_user_func(array($this->container->get($id), $method), $event, $eventName, $this);
            }
        }

        return $event;
    }
}
