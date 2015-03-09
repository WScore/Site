<?php
namespace tests\Enum;

require_once(dirname(__DIR__) . '/autoloader.php');

class EnumTest extends \PHPUnit_Framework_TestCase
{
    public function test0()
    {
        $status = new StatusEnum();
        $choices = StatusEnum::getChoices();
        $this->assertEquals( 'active', $choices[StatusEnum::ACTIVE]);
        $this->assertEquals( 'may be', $choices[StatusEnum::MAY_BE]);
        $this->assertEquals( 'cancel', $choices[StatusEnum::CANCEL]);

        $this->assertEquals('A', $status->value());
        $this->assertEquals('A', (string) $status);
        $this->assertEquals('active', $status->label());
        $this->assertTrue($status->isActive());
        $this->assertFalse($status->isCancel());

        $this->assertEquals($choices, $status->getSelection());

        $yesNo = $status->withYesNo();
        $selects = $yesNo->getSelection();
        $this->assertEquals( 'active', $selects[StatusEnum::ACTIVE]);
        $this->assertFalse( array_key_exists(StatusEnum::MAY_BE, $selects));
        $this->assertEquals( 'cancel', $selects[StatusEnum::CANCEL]);
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     */
    public function using_selection_which_does_not_have_the_value_throws_InvalidArgumentException()
    {
        $status = new StatusEnum(StatusEnum::MAY_BE);
        $status->withYesNo();
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     */
    public function constructor_value_that_does_not_exists_throws_InvalidArgumentException()
    {
        new StatusEnum('bad value');
    }

    /**
     * @test
     * @expectedException \BadMethodCallException
     */
    public function bad_static_value_method_throws_InvalidArgumentException()
    {
        /** @noinspection PhpUndefinedMethodInspection */
        StatusEnum::badValue();
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     */
    public function using_isBad_method_throws_InvalidArgumentException()
    {
        $status = new StatusEnum(StatusEnum::MAY_BE);
        $status->isActive();
        /** @noinspection PhpUndefinedMethodInspection */
        $this->assertFalse($status->isBad());
        /** @noinspection PhpUndefinedMethodInspection */
        $status->badMethod();
    }

    /**
     * @test
     */
    public function use_constant_name_to_construct()
    {
        $status1 = new StatusEnum();
        $status2 = new StatusEnum(StatusEnum::ACTIVE);
        $status3 = StatusEnum::ACTIVE();
        $this->assertEquals($status1, $status2);
        $this->assertEquals($status1, $status3);
    }
}
