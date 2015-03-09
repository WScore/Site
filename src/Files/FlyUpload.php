<?php
namespace WScore\Site\Files;

use League\Flysystem\FilesystemInterface;

class FlyUpload
{
    const FILE_LOC = 'file-loc';

    /**
     * @var string
     */
    protected $name;

    /**
     * @var array
     */
    protected $config;

    /**
     * @var FilesystemInterface
     */
    private $fileSystem;

    /**
     * @param string          $name
     * @param null|string|int $idx
     */
    private function __construct($name, $idx = null)
    {
        if (!isset($_FILES[$name])) {
            throw new \RuntimeException("no such upload file: {$name}");
        }
        $this->name = $name;
        if (is_null($idx)) {
            $this->config($_FILES[$name]);
            return;
        }
        $this->name .= "[{$idx}]";
        if (!isset($_FILES[$name]['name'][$idx])) {
            throw new \RuntimeException("no such upload file: {$this->name}");
        }
        $config = [];
        $keys   = array_keys($_FILES[$name]);
        foreach ($keys as $key) {
            $config[$key] = isset($_FILES[$name][$key][$idx]) ? $_FILES[$name][$key][$idx] : null;
        }
        $this->config($config);
    }

    /**
     * @param string $name
     * @param string $idx
     * @return FlyUpload
     */
    public static function file($name, $idx = null)
    {
        return new self($name, $idx);
    }

    /**
     * @param array $config
     */
    private function config(array $config)
    {
        $this->config                 = $config;
        $this->config[self::FILE_LOC] = $this->getConfig('tmp_name');
        if ($this->passes() && !is_uploaded_file($this->getFileName())) {
            throw new \RuntimeException('not a download file: ' . $this->name);
        }
    }

    /**
     * @param string $key
     * @return null|string
     */
    private function getConfig($key)
    {
        if (isset($this->config[$key])) {
            return htmlspecialchars($this->config[$key], ENT_QUOTES, 'UTF-8');
        }
        return null;
    }

    /**
     * @return int
     */
    public function getErrorCode()
    {
        return $this->getConfig('error');
    }

    /**
     * @return bool
     */
    public function fails()
    {
        return $this->getErrorCode() != UPLOAD_ERR_OK;
    }

    /**
     * @return bool
     */
    public function passes()
    {
        return $this->getErrorCode() == UPLOAD_ERR_OK;
    }

    /**
     * @return string
     */
    public function getFileName()
    {
        return $this->getConfig(self::FILE_LOC);
    }

    /**
     * @param string $storage
     * @return $this
     */
    public function local($storage)
    {
        $this->fileSystem = FlySystems::local($storage);
        return $this;
    }

    /**
     * @param string|FilesystemInterface $moveTo
     * @return $this
     */
    public function move($moveTo)
    {
        if ($this->fileSystem) {
            $this->moveToFile($moveTo);
        } else {
            $fp = fopen($this->getFileName(), 'r+');
            $this->fileSystem->writeStream($moveTo, $fp);
            fclose($fp);
        }
        $this->config[self::FILE_LOC] = $moveTo;
        return $this;
    }

    /**
     * @return resource|false
     */
    public function open()
    {
        $file = $this->getConfig(self::FILE_LOC);
        if ($this->fileSystem) {
            list($stream) = $this->fileSystem->readStream($file);
            return $stream;
        }
        return fopen($file, 'r+');
    }

    /**
     * @param string $moveTo
     */
    private function moveToFile($moveTo)
    {
        $from = $this->getFileName();
        if (!move_uploaded_file($from, $moveTo)) {
            throw new \RuntimeException('cannot move file to: ' . $moveTo);
        }
    }
}