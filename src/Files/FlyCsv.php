<?php
namespace WScore\Site\Files;

use RuntimeException;
use SplFileObject;

class FlyCsv extends SplFileObject
{
    /**
     * @var array
     */
    private $header;

    /**
     * @var array
     */
    private $map;

    /**
     * @param string|resource $filename
     * @param string          $encodeFrom
     * @return FlyCsv
     */
    public static function openSJis($filename, $encodeFrom='SJIS-win')
    {
        $csv = new self(FlyUtils::tempUtf8($filename, $encodeFrom));
        $csv->setFlags(SplFileObject::READ_CSV);
        return $csv;
    }

    /**
     * @param string $delimiter
     * @param string $enclosure
     * @param string $escape
     * @return array]
     */
    public function fgetcsv($delimiter = ",", $enclosure = "\"", $escape = "\\")
    {
        $csv = parent::fgetcsv($delimiter, $enclosure, $escape);
        if ($csv === [null]) {
            return [];
        }
        /*
         * convert normal csv array to hashed-key using header column.
         */
        if ($this->header) {
            $result = array();
            foreach ($csv as $col => $val) {
                $result[$this->header[$col]] = $val;
            }
            $csv = $result;
        }
        /*
         * map csv to key/column array.
         */
        if ($this->map) {
            $result = array();
            foreach ($this->map as $col => $key) {
                if (!isset($csv[$col])) {
                    throw new RuntimeException("column not defined: " . $col);
                }
                $result[$key] = $csv[$col];
            }
            $csv = $result;
        }
        return $csv;
    }

    /**
     * gets CSV data as header.
     *
     * @return array
     */
    public function readHeader()
    {
        $this->header = $this->fgetcsv();
        return $this->header;
    }

    /**
     * @param array $map
     */
    public function setMap(array $map)
    {
        $this->map = $map;
    }
}