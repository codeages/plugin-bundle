<?php

namespace Codeages\PluginBundle\Tests\System\Slot;

use Codeages\PluginBundle\System\Slot\SlotInjectionCollector;
use PHPUnit\Framework\TestCase;

class SlotInjectionCollectorTest extends TestCase
{
    public function testIndex()
    {
        $cacheDir = sys_get_temp_dir();
        $this->removeCache($cacheDir);

        $files = [
            __DIR__.'/Fixtures/slot_1.yml',
            __DIR__.'/Fixtures/slot_2.yml',
            __DIR__.'/Fixtures/slot_3.yml',
        ];
        $collector = new SlotInjectionCollector($files, $cacheDir, true);

        $injections = require $cacheDir.'/slot.php';
        $this->assertCount(3, $injections);
        $this->assertNotEmpty($injections['example.position_1']);
        $this->assertCount(4, $injections['example.position_1']);
        $this->assertEquals('Codeages\PluginBundle\Tests\System\ExamplePositionSlot2', $injections['example.position_1'][0]);
    }

    protected function removeCache($cacheDir)
    {
        if (file_exists($cacheDir.'/slot.php')) {
            unlink($cacheDir.'/slot.php');
        }

        if (file_exists($cacheDir.'/slot.php.meta')) {
            unlink($cacheDir.'/slot.php.meta');
        }
    }
}
