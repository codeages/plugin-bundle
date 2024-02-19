<?php

namespace Codeages\PluginBundle\Tests\Event\Fixture\DemoEvent;

use Codeages\Biz\Framework\Event\Event;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class TestTwoEventSubscribers implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            'test1' => 'onTest1',
            'test2' => 'onTest2',
            'test3' => 'onTest3',
        ];
    }

    public function onTest1(Event $event)
    {
        return 'test1';
    }

    public function onTest2(Event $event)
    {
        return 'test1';
    }

    public function onTest3(Event $event)
    {
        return 'test3';
    }
}
