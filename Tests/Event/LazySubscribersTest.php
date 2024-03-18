<?php

namespace Codeages\PluginBundle\Tests\Event;

use Codeages\PluginBundle\Event\LazySubscribers;
use Codeages\PluginBundle\Tests\Event\Fixture\DemoEvent\TestOneEventSubscribers;
use Codeages\PluginBundle\Tests\Event\Fixture\DemoEvent\TestTwoEventSubscribers;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class LazySubscribersTest extends TestCase
{
    const CACHE_DIR = __DIR__.DIRECTORY_SEPARATOR.'Fixture'.DIRECTORY_SEPARATOR.'app'.DIRECTORY_SEPARATOR.'cache';

    public function testGetEventMapWithoutService()
    {
        $kernel = $this->mockKernel();

        $container = new ContainerBuilder();
        $container->set('kernel', $kernel);

        $lazySubscribers = $this->getMockBuilder('Codeages\PluginBundle\Event\LazySubscribers')
            ->onlyMethods(['getEventMap'])
            ->setConstructorArgs([$container])
            ->getMockForAbstractClass();
        $lazySubscribers->method('getEventMap')
            ->willReturn([]);
        $this->assertIsArray($lazySubscribers->getCallbacks('test'));
        $this->assertEmpty($lazySubscribers->getCallbacks('test'));
    }

    public function testGetCallbacks()
    {
        $services = [
            'test_one_event_subscribers' => [0 => []],
            'test_two_event_subscribers' => [0 => []],
        ];
        $kernel = $this->mockKernel();

        $container = new ContainerBuilder();
        $container->set('kernel', $kernel);
        $container->set('test_one_event_subscribers', new TestOneEventSubscribers());
        $container->set('test_two_event_subscribers', new TestTwoEventSubscribers());

        $lazySubscribers = new LazySubscribers($container);

        foreach ($services as $id => $tags) {
            $lazySubscribers->addSubscriberService($id);
        }

        $test1 = [
            ['test_one_event_subscribers', 'onTest1', 0],
            ['test_two_event_subscribers', 'onTest1', 0],
        ];
        $test2 = [
            ['test_two_event_subscribers', 'onTest2', 0],
            ['test_one_event_subscribers', 'onTest2', -100],
        ];
        $test3 = [
            ['test_one_event_subscribers', 'onTest3', 100],
            ['test_two_event_subscribers', 'onTest3', 0],
        ];

        $test1Callbacks = $lazySubscribers->getCallbacks('test1');
        $this->assertEquals($test1, $test1Callbacks);

        $test2Callbacks = $lazySubscribers->getCallbacks('test2');
        $this->assertEquals($test2, $test2Callbacks);

        $test3Callbacks = $lazySubscribers->getCallbacks('test3');
        $this->assertEquals($test3, $test3Callbacks);

        unlink(self::CACHE_DIR.DIRECTORY_SEPARATOR.'event_map.php');
        unlink(self::CACHE_DIR.DIRECTORY_SEPARATOR.'event_map.php.meta');
    }

    private function mockKernel()
    {
        $testKernel = $this->getMockBuilder('Codeages\PluginBundle\Tests\Event\Fixture\TestKernel')
            ->setConstructorArgs(['test', false])
            ->onlyMethods(['getCacheDir'])
            ->getMockForAbstractClass();

        $testKernel->method('getCacheDir')
            ->willReturn(self::CACHE_DIR);

        if (file_exists(self::CACHE_DIR.DIRECTORY_SEPARATOR.'event_map.php')) {
            unlink(self::CACHE_DIR.DIRECTORY_SEPARATOR.'event_map.php');
        }

        if (file_exists(self::CACHE_DIR.DIRECTORY_SEPARATOR.'event_map.php.meta')) {
            unlink(self::CACHE_DIR.DIRECTORY_SEPARATOR.'event_map.php.meta');
        }

        return $testKernel;
    }
}
