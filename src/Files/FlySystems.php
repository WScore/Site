<?php
namespace WScore\Site\Files;

use Aura\Auth\Adapter\NullAdapter;
use League\Flysystem\Adapter\Ftp;
use League\Flysystem\Adapter\Local;
use League\Flysystem\CacheInterface;
use League\Flysystem\Filesystem;

class FlySystems
{
    /**
     * local file system.
     *
     * @param string         $dir
     * @param CacheInterface $cache
     * @return Filesystem
     */
    public static function local($dir, $cache=null)
    {
        return new Filesystem(new Local($dir), $cache);
    }

    /**
     * ftp connector.
     *
     * defaults are:
     * $config['port']    = true
     * $config['ssl']     = true
     * $config['passive'] = true
     * $config['timeout'] = 10
     *
     * @param string $host
     * @param string $user
     * @param string $pass
     * @param string $root
     * @param array  $config
     * @return Filesystem
     */
    public static function ftp($host, $user, $pass, $root='/', $config=[])
    {
        $config += [
            'host' => $host,
            'username' => $user,
            'password' => $pass,
            'port' => 21,
            'root' => $root,
            'ssl' => true,
            'passive' => true,
            'timeout' => 10,
        ];
        return new Filesystem(new Ftp($config));
    }

    /**
     * null and void world.
     *
     * @return Filesystem
     */
    public static function null()
    {
        return new Filesystem(new NullAdapter());
    }
}