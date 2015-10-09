<?php
use WScore\Site\Builder\AppBuilder;

/**
 * @var AppBuilder $builder
 */

$builder->set('DB-Key', 'local key');

/**
 * return array of environments.
 */
return ['local', 'tests'];