<?php

use WScore\Site\Builder\AppBuilder;

require_once dirname(dirname(__DIR__)) . '/autoloader.php';

/**
 * @var AppBuilder $builder
 */
$builder = include __DIR__ . '/build.php';
$builder->configure('Application/routes');

print_r($builder);

return $builder->app;