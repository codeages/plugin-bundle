<?php

namespace Codeages\PluginBundle\Tests\Event;

use Codeages\Biz\Framework\Event\Event;
use Codeages\PluginBundle\Event\LazyDispatcher;
use Codeages\PluginBundle\Event\LazySubscribers;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class LazyDispatcherTest extends TestCase
{
    const CACHE_DIR = __DIR__.DIRECTORY_SEPARATOR.'Fixture'.DIRECTORY_SEPARATOR.'app'.DIRECTORY_SEPARATOR.'cache';

    public function testDispatch()
    {
        $kernel = $this->mockKernel();

        $container = new ContainerBuilder();
        $container->set('kernel', $kernel);
        $container->set('codeags_plugin.event.lazy_subscribers', new LazySubscribers($container));

        $lazyDispatcher = new LazyDispatcher($container);
        $lazyDispatcher->dispatch(new Event(), 'test4');

        $this->assertTrue(true);

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
            ->willReturn(__DIR__.DIRECTORY_SEPARATOR.'Fixture'.DIRECTORY_SEPARATOR.'app'.DIRECTORY_SEPARATOR.'cache');

        if (file_exists(self::CACHE_DIR.DIRECTORY_SEPARATOR.'event_map.php')) {
            unlink(self::CACHE_DIR.DIRECTORY_SEPARATOR.'event_map.php');
        }

        if (file_exists(self::CACHE_DIR.DIRECTORY_SEPARATOR.'event_map.php.meta')) {
            unlink(self::CACHE_DIR.DIRECTORY_SEPARATOR.'event_map.php.meta');
        }

        return $testKernel;
    }
}
