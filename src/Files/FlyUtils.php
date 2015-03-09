<?php
namespace WScore\Site\Files;

class FlyUtils
{
    /**
     * returns a temporary file name that have the converted encoding contents.
     *
     * @param string|resource $file
     * @param string          $from
     * @param string          $to
     * @return string
     */
    public static function tempUtf8($file, $from = null, $to = 'UTF-8')
    {
        $tmp_file = tempnam(sys_get_temp_dir(), 'file');
        $fileFp   = is_resource($file) ? $file : fopen($file, 'r+');
        $tempFp   = fopen($tmp_file, 'w+');
        self::convertEncoding($fileFp, $tempFp, $from, $to);
        while (!feof($fileFp)) {
            $text = fgets($fileFp);
            fwrite($tempFp, mb_convert_encoding($text, $to, $from));
        }
        fclose($fileFp);
        rewind($tempFp);
        fclose($tempFp);
        return $tmp_file;
    }

    /**
     * converts encoding from a resource to another resource.
     *
     * @param resource $from_fp
     * @param resource $to_fp
     * @param string   $from
     * @param string   $to
     */
    public static function convertEncoding($from_fp, $to_fp, $from, $to = 'UTF-8')
    {
        rewind($from_fp);
        rewind($to_fp);
        while (!feof($from_fp)) {
            fwrite($to_fp, mb_convert_encoding(fgets($from_fp), $to, $from));
        }
    }
}