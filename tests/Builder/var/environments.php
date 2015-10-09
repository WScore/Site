<?php
use WScore\Site\Builder\AppBuilder;

/**
 * @var AppBuilder $builder
 */

$builder->set('DB-Key', 'this is a secret key set in environments.php file');

/**
 * return nothing (or empty array) for production environment.
 */
//return [];