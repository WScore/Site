<?php
namespace tests\Files;

use WScore\Site\Files\FlySync;
use WScore\Site\Files\FlySystems;

require_once(dirname(__DIR__) . '/autoloader.php');

class SyncTest extends \PHPUnit_Framework_TestCase
{
    function test0()
    {
        $sync = new FlySync(
            FlySystems::local(__DIR__.'/sync-test'),
            FlySystems::null()
        );
        $this->assertEquals('WScore\Site\Files\FlySync', get_class($sync));
    }

    /**
     * @test
     */
    function sync_from_local_to_null()
    {
        $null = FlySystems::null();
        $this->assertFalse($null->has('/test0.txt'));
        $sync = new FlySync(
            FlySystems::local(__DIR__.'/sync-test'),
            $null
        );
        $sync->checkTimeStamp();
        $sync->syncDir('/');
        $this->assertTrue($null->has('/test0.txt'));
    }

    /**
     * @test
     */
    function sync_with_check_time()
    {
        $syncFrom = __DIR__.'/sync-test';
        $syncTo = __DIR__.'/sync-done';
        if(!file_exists($syncTo)) {
            mkdir($syncTo, 0777);
        }
        file_put_contents($syncTo.'/test0.txt', 'tested time stamp update');
        $sync = new FlySync(
            FlySystems::local($syncFrom),
            FlySystems::local($syncTo)
        );
        $sync->checkTimeStamp();
        $sync->syncDir('/');

        $this->assertEquals('tested time stamp update', file_get_contents($syncTo.'/test0.txt'));
    }
}