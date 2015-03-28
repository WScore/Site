<?php
namespace tests\DateTime;

use WScore\Site\DateTime\Date;

require_once(dirname(__DIR__) . '/autoloader.php');

class EnumTest extends \PHPUnit_Framework_TestCase
{
    public function test0()
    {
        $date = new Date();
        $this->assertEquals('WScore\Site\DateTime\Date', get_class($date));
        $this->assertEquals('WScore\Site\DateTime\Compare', get_class($date->is));
    }

    /**
     * @test
     */
    function accessing_by_property()
    {
        $d = new Date('1989-01-08 12:23:34');
        $this->assertEquals( '1989', $d->year);
        $this->assertEquals( '1',  $d->month);
        $this->assertEquals( '8',  $d->day);
        $this->assertEquals( '12', $d->hour);
        $this->assertEquals( '23', $d->minute);
        $this->assertEquals( '34', $d->second);
        $this->assertEquals( '0',  $d->dayOfWeek);
        $this->assertEquals( '7',  $d->dayOfYear);
        $this->assertEquals( '31', $d->daysInMonth);
        $this->assertEquals( '600233014', $d->timestamp);
    }
    
    /**
     * @test
     */
    function genGou()
    {
        $h = new Date('1989-01-08');
        $this->assertEquals( '1989-01-08 00:00:00', (string) $h);
        $this->assertEquals( '平成', $h->format('%G'));
        $this->assertEquals( '昭和', $h->modify('-1 day')->format('%G'));
        $this->assertEquals( '平成 元年 日 日曜日', $h->format('%G %Y年 %w %W'));
        $this->assertEquals( '平成 2年', $h->modify('1 year')->format('%G %Y年'));
        
        $this->assertEquals( '平成', $h->jaGenGou());
        $this->assertEquals( '昭和', $h->modify('-1 day')->jaGenGou());

        $h = new Date('1868-01-25');
        $this->assertEquals( '明治', $h->format('%G'));
        $this->assertEquals( '', $h->modify('-1 day')->format('%G'));
    }
}
