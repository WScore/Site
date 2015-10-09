<?php
use WScore\Site\Builder\AppBuilder;

/**
 * @var AppBuilder $builder
 */

$builder->set('DB-Key', 'this is a secret key set in environments.php file');

/**
 * return array of environments.
 */
return ['local', 'tests'];