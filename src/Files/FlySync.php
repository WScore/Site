<?php
namespace WScore\Site\Files;

use League\Flysystem\FilesystemInterface;
use League\Flysystem\MountManager;

class FlySync
{
    /**
     * @var MountManager
     */
    private $sync;

    /**
     * @var bool
     */
    private $checkTimeStamp = true;

    /**
     * @param FilesystemInterface $from
     * @param FilesystemInterface $to
     */
    public function __construct($from, $to)
    {
        $this->sync = new MountManager([
            'from' => $from,
            'to'   => $to,
        ]);
    }

    /**
     * @param bool $check
     * @return $this
     */
    public function checkTimeStamp($check = true)
    {
        $this->checkTimeStamp = $check;
        return $this;
    }

    /**
     * @param string $dir
     */
    public function syncDir($dir = '')
    {
        $sync  = $this->sync;
        $list  = $sync->listContents('from://' . $dir);
        $files = [];
        foreach ($list as $entry) {
            $files[] = $entry['path'];
        }
        $this->syncFiles($files);
    }

    /**
     * @param array $list
     */
    public function syncFiles(array $list)
    {
        $sync = $this->sync;
        foreach ($list as $entry) {
            $update   = false;
            $pathFrom = 'from://' . $entry;
            $pathTo   = 'to://' . $entry;
            if (!$sync->has($pathTo)) {
                $update = true;
            } elseif ($this->checkTimeStamp &&
                $sync->getTimestamp($pathFrom) > $sync->getTimestamp($pathTo)
            ) {
                $update = true;
            }
            if ($update) {
                $sync->put($pathTo, $sync->read($pathFrom));
            }
        }
    }
}