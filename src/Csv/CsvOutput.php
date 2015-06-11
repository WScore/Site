<?php
namespace WScore\Site\Csv;

use SplTempFileObject;

/**
 * Class CsvOutput
 *
 * 主としてエクセル用のCSV出力クラス。
 * Shift-JISまたはUTF-16LEでの出力が可能。
 *
 * 特徴：
 *
 * 何でもかんでもカンマ（"）で囲ってしまう。
 * データ内に入っているカンマをエスケープするだけでOK。
 *
 * SJISの場合は、カンマ区切り、CRLF改行。
 * UTF16の場合は、タブ区切り、CRLF改行。
 *
 * 注意：
 *
 * A1のセルに「ID」または「ID_xxxx」が入っているとSYLKエラー。
 * http://www.yamamototakashi.com/soft/d2/manual/HLP000206.html
 *
 */
class CsvOutput
{
    /**
     * @var string
     */
    private $from = 'UTF-8';

    /**
     * @var string
     */
    private $toEncode = 'SJIS-win';

    /**
     * @var string
     */
    private $delimeter = ',';

    /**
     * @var bool
     */
    private $withBom = false;

    /**
     * @var SplTempFileObject
     */
    private $file;

    /**
     * @param bool|null $withBom
     */
    protected function __construct($withBom = null)
    {
        if (is_bool($withBom)) {
            $this->withBom = $withBom;
        }
        $this->file = new SplTempFileObject();
    }

    /**
     * creates a csv in Shift-JIS (cp932) character encoding.
     *
     * @return static
     */
    public static function toSjisExcel()
    {
        $csv            = new static(false);
        $csv->toEncode  = 'SJIS-win';
        $csv->withBom   = false;
        $csv->delimeter = ',';
        return $csv;
    }

    /**
     * creates a csv in UTF-16LE with BOM character encoding.
     *
     * @return static
     */
    public static function toUtf16Excel()
    {
        $csv            = new static(false);
        $csv->toEncode  = 'UTF-16LE';
        $csv->withBom   = chr(255) . chr(254);
        $csv->delimeter = "\t";
        return $csv;
    }

    /**
     * puts a $csv data.
     *
     * @param array $csv
     */
    public function put(array $csv)
    {
        $from = $this->from;
        $to   = $this->toEncode;
        array_walk($csv, function (&$v) use ($from, $to) {
            $v = str_replace('"', '""', $v);
            $v = str_replace("\n", chr(10), $v);
        });
        $line = '"' . implode("\"{$this->delimeter}\"", $csv) . '"' . "\r\n";
        $line = mb_convert_encoding($line, $to, $from);
        $this->file->fwrite($line);
    }

    /**
     * emits a http header for downloading the csv data.
     *
     * @param string $filename
     * @param bool   $download
     * @return $this
     */
    public function header($filename, $download = true)
    {
        $attach = $download ? 'attachment' : 'inline';
        header('Content-Type: application/octet-stream');
        header('Content-Length: ' . $this->file->ftell());
        header("Content-Disposition: {$attach}; filename=\"{$filename}\"");
        return $this;
    }

    /**
     * emits the csv data.
     *
     */
    public function emit()
    {
        $this->file->rewind();
        $this->file->fpassthru();
    }
}