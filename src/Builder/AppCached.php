<?php
namespace WScore\Site\Builder;

/**
 * Class AppCached
 *
 * EXPERIMENTAL!
 *
 *
 * @package WScore\Site\Builder
 */
class AppCached extends AppBuilder
{
    /**
     * caches entire Application, $app, to a file.
     *
     * specify $closure to construct the application in case cache file is absent.
     *
     * @param callable $closure
     * @return $this
     */
    public function setup(callable $closure)
    {
        $cached = $this->var_dir . '/app.cached';
        if (!$this->debug && file_exists($cached)) {
            return unserialize(\file_get_contents($cached));
        }
        parent::setup($closure);
        if (!$this->debug) {
            \file_put_contents($cached, serialize($this));
            chmod($cached, 0666);
        }

        return $this;
    }

    /**
     * @param array       $data
     * @param string      $key
     * @param string|null $value
     * @return $this
     */
    public function clearCacheIf(array $data, $key, $value = null)
    {
        if (!array_key_exists($key, $data)) {
            return $this;
        }
        if (is_null($value) || $data[$key] === $value) {
            $this->clearCachedFile();
        }
        return $this;
    }

    /**
     *
     */
    private function clearCachedFile()
    {
        unlink($this->var_dir . '/app.cached');
    }
}