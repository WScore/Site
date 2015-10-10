<?php
namespace WScore\Site\Builder;

/**
 * Class AppBuilder
 *
 * a generic application builder for environment aware process.
 *
 * @package WScore\Site\Builder
 */
class AppBuilder
{
    /**
     * @var mixed       the application to configure
     */
    public $app = null;

    /**
     * @var string      main config dir (ex: project-root/app/)
     */
    public $app_dir;

    /**
     * @var string      var dir (no version control) (ex: project-root/vars)
     */
    public $var_dir;

    /**
     * @var array       list of environment
     */
    public $environments = [''];

    /**
     * @var bool        debug or not
     */
    public $debug = false;

    /**
     * @var array
     */
    private $container = [];

    /**
     * @param string      $config_dir
     * @param string|null $var_dir
     */
    public function __construct($config_dir, $var_dir = null)
    {
        // default configuration.
        $this->app_dir = $config_dir;
        $this->var_dir = $var_dir ?: dirname($config_dir) . '/var';
    }

    /**
     * @param string      $config_dir
     * @param string|null $var_dir
     * @return AppBuilder
     */
    public static function forge($config_dir, $var_dir = null)
    {
        return new self($config_dir, $var_dir);
    }

    /**
     * @param callable $callable
     * @return $this
     */
    public function setup(callable $callable)
    {
        $callable($this);

        return $this;
    }

    /**
     * read multiple configuration files at $this->config_dir/$file.
     *
     * this reads multiple configuration files under $config_dir.
     * if $dir = mail/mail, configuration files are,
     *   - mail/mail.php
     *   - mail/mail-debug.php
     *   - mail/mail-{$environment}.php
     *
     * @param string $config
     * @param bool   $envOnly
     * @return $this
     */
    public function configure($config, $envOnly = false)
    {
        if ($envOnly) {
            $env_list = $this->environments;
        } else {
            $env_list = $this->debug ? ['', 'debug'] : [''];
            $env_list = array_merge($env_list, $this->environments);
        }
        $directory = $this->app_dir . DIRECTORY_SEPARATOR;
        foreach ($env_list as $env) {
            $file = ($env ? $env . '/' : '') . $config;
            $this->evaluate($directory . $file);
        }

        return $this;
    }

    /**
     * evaluate PHP file at {$__config}.php and returns the value.
     *
     * @param string $__file
     * @return mixed|null
     */
    public function evaluate($__file)
    {
        $__file = $__file . '.php';
        if (!file_exists($__file)) {
            return null;
        }
        /** @noinspection PhpUnusedLocalVariableInspection */
        $app = $this->app;
        /** @noinspection PhpUnusedLocalVariableInspection */
        $builder = $this;

        /** @noinspection PhpIncludeInspection */

        return include($__file);
    }

    /**
     * loads the environment based configuration.
     *
     * @param string $env_file
     * @return $this
     */
    public function loadEnvironment($env_file)
    {
        $environments = $this->evaluate($env_file);
        if ($environments !== 1) {
            $this->environments = (array)$environments;
        }

        return $this;
    }

    /**
     * sets $value as $key in local container.
     *
     * @param string $key
     * @param mixed  $value
     * @return $this
     */
    public function set($key, $value)
    {
        $this->container[$key] = $value;

        return $this;
    }

    /**
     * gets $key from the local container.
     *
     * @param string     $key
     * @param null|mixed $default
     * @return mixed
     */
    public function get($key, $default = null)
    {
        return array_key_exists($key, $this->container) ? $this->container[$key] : $default;
    }

    /**
     * @param string $key
     * @return bool
     */    
    public function has($key)
    {
        return array_key_exists($key, $this->container);
    }
}