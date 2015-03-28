<?php
namespace tests\DateTime;

use WScore\Site\DateTime\DateTime;
use WScore\Site\DateTime\Diff;

require_once(dirname(__DIR__) . '/autoloader.php');

class DiffTest extends \PHPUnit_Framework_TestCase
{
    function test0()
    {
        $date = new DateTime();
        $this->assertEquals('WScore\Site\DateTime\DateTime', get_class($date));
        $this->assertEquals('WScore\Site\DateTime\Diff', get_class($date->diff));
    }

    /**
     * @test
     */
    function diff_always_returns_new_instance()
    {
        $date = new DateTime();
        $this->assertEquals($date->diff, $date->diff);
        $this->assertNotSame($date->diff, $date->diff);
    }

    /**
     * @test
     */
    function diff_in_days_months_years()
    {
        $d0 = DateTime::createDate('2015', '03', '04');
        $d1 = DateTime::createDate('2016', '04', '07');
        $d2 = $d0->modify('400 days');
        $this->assertEquals(1, $d0->diff->inYears($d1));
        $this->assertEquals(13, $d0->diff->inMonths($d1));
        $this->assertEquals(400, $d0->diff->inDays($d2));

        // a bit less than a year.
        $d1 = DateTime::createDate('2016', '02', '07');
        $this->assertEquals(0, $d0->diff->inYears($d1));
        $this->assertEquals(11, $d0->diff->inMonths($d1));
    }

    /**
     * @test
     */
    function diff_in_hours_min_sec()
    {
        $d0 = DateTime::createTime('12', '23', '34');
        $d1 = DateTime::createTime('13', '34', '45');
        $d2 = $d0->modify('400 seconds');
        $this->assertEquals(1, $d0->diff->inHours($d1));
        $this->assertEquals(71, $d0->diff->inMinutes($d1));
        $this->assertEquals(400, $d0->diff->inSeconds($d2));

        $d3 = DateTime::createTime('13', '12', '45');
        $this->assertEquals(0, $d0->diff->inHours($d3));
        $this->assertEquals(49, $d0->diff->inMinutes($d3));
    }

    /**
     * @test
     */
    function use_Diff_directly()
    {
        $d0 = DateTime::createDate('2015', '03', '04');
        $d1 = DateTime::createDate('2016', '04', '07');
        $this->assertEquals(1, Diff::start($d0)->inYears($d1));
        $this->assertEquals(13, Diff::start($d0)->inMonths($d1));
    }
}