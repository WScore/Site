<?php
namespace tests\DateTime;

use WScore\Site\DateTime\Date;

require_once(dirname(__DIR__) . '/autoloader.php');

class EnumTest extends \PHPUnit_Framework_TestCase
{
    public function test0()
    {
        $this->assertEquals('WScore\Site\DateTime\Date', get_class(new Date()));
    }
    
}
