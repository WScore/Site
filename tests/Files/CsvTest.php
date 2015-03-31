<?php
namespace tests\Files;

use WScore\Site\Files\FlyCsv;

require_once(dirname(__DIR__) . '/autoloader.php');

class SwiftTest extends \PHPUnit_Framework_TestCase
{
    function test0()
    {
        $this->assertEquals('WScore\Site\Files\FlyCsv', FlyCsv::class);
    }

    /**
     * @test
     */
    function reads_csv_data_in_utf8()
    {
        $csv = new FlyCsv(__DIR__.'/test-utf8.csv');
        $data = $csv->fgetcsv();
        $this->assertEquals('id', $data[0]);
        $this->assertEquals('name', $data[1]);
        $data = $csv->fgetcsv();
        $this->assertEquals('1', $data[0]);
        $this->assertEquals('テスト　太郎', $data[1]);
    }

    /**
     * @test
     */
    function reads_csv_data_in_shift_jis()
    {
        $csv = FlyCsv::openSJis(__DIR__.'/test-sjis.csv');
        $data = $csv->fgetcsv();
        $this->assertEquals('id', $data[0]);
        $this->assertEquals('name', $data[1]);
        $data = $csv->fgetcsv();
        $this->assertEquals('1', $data[0]);
        $this->assertEquals('テスト　太郎', $data[1]);
    }

    /**
     * @test
     */
    function reads_csv_data_with_header()
    {
        $csv = FlyCsv::openSJis(__DIR__.'/test-sjis.csv');
        $data = $csv->readHeader();
        $this->assertEquals('id', $data[0]);
        $this->assertEquals('name', $data[1]);
        $data = $csv->fgetcsv();
        $this->assertEquals('1', $data['id']);
        $this->assertEquals('テスト　太郎', $data['name']);
    }

    /**
     * @test
     */
    function fgetcsv_returns_empty_array_at_the_end()
    {
        $csv = new FlyCsv(__DIR__.'/test-utf8.csv');
        while($data = $csv->fgetcsv()) {
            $this->assertArrayHasKey('1', $data);
        }
    }

    /**
     * @test
     */
    function map_will_convert_header_to_mapped_code()
    {
        $csv = FlyCsv::openSJis(__DIR__.'/test-sjis.csv');
        $csv->readHeader();
        $csv->setMap([
            'id' => 'id',
            'mail' => 'e-mail'
        ]);
        $data = $csv->fgetcsv();
        $this->assertEquals('1', $data['id']);
        $this->assertEquals('email@example.com', $data['e-mail']);
    }

    /**
     * @test
     * @expectedException \RuntimeException
     */
    function map_will_throw_exception_if_failed_to_map()
    {
        $csv = FlyCsv::openSJis(__DIR__.'/test-sjis.csv');
        $csv->readHeader();
        $csv->setMap([
            'bad' => 'throw',
            'mail' => 'e-mail'
        ]);
        $data = $csv->fgetcsv();
        $this->assertEquals('1', $data['id']);
        $this->assertEquals('email@example.com', $data['e-mail']);
    }
}
