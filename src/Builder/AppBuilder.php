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
    public $config_dir;

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
        $this->config_dir = $config_dir;
        $this->var_dir    = $var_dir ?: dirname($config_dir) . '/var';
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
     * @param string $base
     * @param bool   $envOnly
     * @return $this
     */
    public function configure($base, $envOnly = false)
    {
        if ($envOnly) {
            $env_list = $this->environments;
        } else {
            $env_list = $this->debug ? ['', 'debug'] : [''];
            $env_list = array_merge($env_list, $this->environments);
        }
        $directory = $this->config_dir . DIRECTORY_SEPARATOR;
        foreach ($env_list as $env) {
            $file = ($env ? $env . '/' : '') . $base;
            $this->evaluatePhp($directory . $file);
        }
        return $this;
    }

    /**
     * read configuration at $this->config_dir/{$name}.php.
     *
     * @param string $name
     * @return mixed|null
     */
    public function evaluate($name)
    {
        $file = $this->config_dir . DIRECTORY_SEPARATOR . $name;
        return $this->evaluatePhp($file);
    }

    /**
     * includes a $__config.php file.
     *
     * the $__config is an absolute path (without .php extension).
     *
     * @param string $__config
     * @return mixed|null
     */
    private function evaluatePhp($__config)
    {
        $__config = $__config . '.php';
        if (!file_exists($__config)) {
            return null;
        }
        /** @noinspection PhpUnusedLocalVariableInspection */
        $app = $this->app;
        /** @noinspection PhpUnusedLocalVariableInspection */
        $builder = $this;
        /** @noinspection PhpIncludeInspection */
        return include($__config);
    }

    /**
     * loads the environment based configuration.
     *
     * @param string $env_file
     * @return $this
     */
    public function loadEnvironment($env_file)
    {
        $environments = $this->evaluatePhp($env_file);
        if ($environments !== 1) {
            $this->environments = (array) $environments;
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
}