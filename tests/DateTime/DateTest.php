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
    
}
