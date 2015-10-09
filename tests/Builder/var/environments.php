<?php
use WScore\Site\Builder\AppBuilder;

/**
 * @var AppBuilder $builder
 */

$builder->set('DB-Key', 'secret key');

/**
 * return nothing (or empty array) for production environment.
 */
//return [];