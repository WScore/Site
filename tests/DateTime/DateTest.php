<?php
namespace tests\DateTime;

use WScore\Site\DateTime\Date;

require_once(dirname(__DIR__) . '/autoloader.php');

class DateTest extends \PHPUnit_Framework_TestCase
{
    public function test0()
    {
        $date = new Date();
        $this->assertEquals('WScore\Site\DateTime\Date', get_class($date));
    }

    /**
     * @test
     */
    function now_returns_same_object()
    {
        // calling now() returns the same Date object.
        $d0 = Date::now();
        $d1 = Date::now();
        $this->assertSame($d0, $d1);

        // call now() returns the new Date with current time.
        $d2 = Date::now(true);
        $this->assertNotSame($d0, $d2);

        // specifying $time will return a new Date object.
        $d3 = Date::now('2015-03-28 12:23:34');
        $this->assertNotSame($d0, $d3);

        // specifying false resets the date.
        $dn = Date::now(false);
        $d4 = Date::now();
        $this->assertNull($dn);
        $this->assertNotSame($d0, $d4);
    }

    /**
     * @test
     */
    function Date_is_immutable()
    {
        $d0 = new Date('2015-03-28 12:23:34');
        $d1 = $d0->modify('0 day');
        $this->assertEquals($d0, $d1);
        $this->assertNotSame($d0, $d1);
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
        $this->assertEquals( 1868, $h->modify('-1 day')->format('%Y'));
        $this->assertEquals( '', $h->modify('-1 day')->format('%G'));
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     */
    function non_existing_property_throws_an_exception()
    {
        $d = new Date();
        /** @noinspection PhpUndefinedFieldInspection */
        $d->badProperty;
    }

    /**
     * @test
     */
    function format_with_unknown_code()
    {
        $this->assertEquals('x%x', (new Date())->format('x%x'));
    }
}
