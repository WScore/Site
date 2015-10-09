<?php
use WScore\Site\Builder\AppBuilder;

/**
 * builds application.
 */
$builder = AppBuilder::forge(__DIR__)
    ->setup(function (AppBuilder $builder) {
        $builder->app = 'built in build.php';
        $builder->loadEnvironment($builder->var_dir . '/env-local-tests');
        $builder->configure('config/test');
        $builder->configure('config/only', true);
    });

return $builder;