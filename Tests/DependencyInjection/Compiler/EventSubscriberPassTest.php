<?php

namespace Codeages\PluginBundle\Tests\DependencyInjection\Compiler;

use Codeages\PluginBundle\DependencyInjection\Compiler\EventSubscriberPass;
use PHPUnit\Framework\TestCase;

class EventSubscriberPassTest extends TestCase
{
    public function testEventSubscriberWithoutInterface()
    {
        $services = [
            'test_event_subscriber' => [0 => []],
        ];
        $definition = $this->getMockBuilder('Symfony\Component\DependencyInjection\Definition')->getMock();

        $builder = $this->getMockBuilder('Symfony\Component\DependencyInjection\ContainerBuilder')->onlyMethods(['findTaggedServiceIds', 'getDefinition'])->getMock();

        $builder->expects($this->atLeastOnce())
            ->method('findTaggedServiceIds')
            ->will($this->onConsecutiveCalls([], $services));

        $builder->expects($this->any())
            ->method('getDefinition')
            ->will($this->returnValue($definition));

        $eventSubscriberPass = new EventSubscriberPass();
        $eventSubscriberPass->process($builder);
    }
}
