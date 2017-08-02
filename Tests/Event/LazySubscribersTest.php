<?php

namespace Codeages\PluginBundle\Tests\Event;

use Codeages\PluginBundle\Event\LazySubscribers;
use Codeages\PluginBundle\Tests\Event\Fixture\DemoEvent\TestOneEventSubscribers;
use Codeages\PluginBundle\Tests\Event\Fixture\DemoEvent\TestTwoEventSubscribers;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class LazySubscribersTest extends TestCase
{
    public function testGetEventMapWithoutService()
    {
        $kernel = $this->mockKernel();

        $container = new ContainerBuilder();
        $container->set('kernel', $kernel);

        $lazySubscribers = $this->getMockBuilder('Codeages\PluginBundle\Event\LazySubscribers')
            ->setMethods(array('getEventMap'))
            ->setConstructorArgs(array($container))
            ->getMockForAbstractClass();
        $lazySubscribers->method('getEventMap')
            ->willReturn(array());

    }

    public function testGetCallbacks()
    {
        $services = array(
            'test_one_event_subscribers' => array(0 => array()),
            'test_two_event_subscribers' => array(0 => array()),

        );
        $kernel = $this->mockKernel();

        $container = new ContainerBuilder();
        $container->set('kernel', $kernel);
        $container->set('test_one_event_subscribers', new TestOneEventSubscribers());
        $container->set('test_two_event_subscribers', new TestTwoEventSubscribers());

        $lazySubscribers = new LazySubscribers($container);

        foreach ($services as $id => $tags) {
            $lazySubscribers->addSubscriberService($id);
        }

        $cacheFilePath = __DIR__.DIRECTORY_SEPARATOR.'Fixture'.DIRECTORY_SEPARATOR.'app'.DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR.'event_map.php';
        $eventMap = array();
//        try{
            $lazySubscribers->generateCache();
//        }catch (\Exception $e) {
//            var_dump($e->getMessage());
//
//        }
        var_dump($eventMap);

    }

    private function mockKernel()
    {
        $testKernel = $this->getMockBuilder('Codeages\PluginBundle\Tests\Event\Fixture\TestKernel')
            ->setConstructorArgs(array('test',false))
            ->setMethods(array('getCacheDir'))
            ->getMockForAbstractClass();

        $testKernel->method('getCacheDir')
            ->willReturn(__DIR__.DIRECTORY_SEPARATOR.'Fixture'.DIRECTORY_SEPARATOR.'app'.DIRECTORY_SEPARATOR.'cache');

        return $testKernel;
    }


}
