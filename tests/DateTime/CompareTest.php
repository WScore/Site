<?php
namespace tests\DateTime;

use WScore\Site\DateTime\DateTime;

require_once(dirname(__DIR__) . '/autoloader.php');

class CompareTest extends \PHPUnit_Framework_TestCase
{
    function test0()
    {
        $date = new DateTime();
        $this->assertEquals('WScore\Site\DateTime\DateTime', get_class($date));
        $this->assertEquals('WScore\Site\DateTime\Compare', get_class($date->is));
    }

    /**
     * @return DateTime[]
     */
    function getDates()
    {
        $d = new DateTime();
        $d1 = $d->modify('1 day');
        $d2 = $d->modify('2 day');
        $d3 = $d->modify('3 day');
        return [$d1, $d2, $d3];
    }

    /**
     * @test
     */
    function between()
    {
        list($d1, $d2, $d3) = $this->getDates();

        // between
        $this->assertTrue($d2->is->between($d1, $d3));
        $this->assertTrue($d2->is->between($d3, $d1));
        $this->assertTrue($d2->is->between($d1, $d2));
        $this->assertTrue($d2->is->between($d2, $d3));

        // between but excluding equal
        $this->assertTrue( $d2->is->between($d1, $d3, false));
        $this->assertFalse($d2->is->between($d1, $d2, false));
        $this->assertFalse($d2->is->between($d2, $d3, false));
    }

    /**
     * @test
     */
    function gt_lt_eq()
    {
        list($d1, $d2, $d3) = $this->getDates();

        // gt, eq, lt
        $this->assertTrue($d2->is->gt($d1));
        $this->assertTrue($d2->is->eq($d2));
        $this->assertTrue($d2->is->lt($d3));
        $this->assertTrue($d2->is->ne($d3));

        // gt, eq, lt
        $this->assertFalse($d2->is->lt($d1));
        $this->assertFalse($d2->is->ne($d2));
        $this->assertFalse($d2->is->gt($d3));
        $this->assertFalse($d2->is->eq($d3));
    }

    /**
     * @test
     */
    function gte_lte()
    {
        list($d1, $d2, $d3) = $this->getDates();

        // gt3, lte
        $this->assertTrue($d2->is->gte($d1));
        $this->assertTrue($d2->is->lte($d3));

        // being equal
        $this->assertTrue($d2->is->gte($d2));
        $this->assertTrue($d2->is->lte($d2));

        // gt3, lte
        $this->assertFalse($d2->is->lte($d1));
        $this->assertFalse($d2->is->gte($d3));
    }

    /**
     * @test
     */
    function min_max()
    {
        list($d1, $d2, $d3) = $this->getDates();

        $this->assertSame($d1, $d2->is->min($d1));
        $this->assertSame($d2, $d2->is->min($d3));

        $this->assertSame($d2, $d2->is->max($d1));
        $this->assertSame($d3, $d2->is->max($d3));
    }

    /**
     * @test
     */
    function sameDate()
    {
        /** @var DateTime $d1 */
        list($d1) = $this->getDates();
        $d2 = $d1->modify('1 hour');
        $this->assertNotEquals($d1, $d2);
        $this->assertEquals($d1->ymd, $d2->ymd);
        $this->assertTrue($d1->is->sameDate($d2));
    }
}
