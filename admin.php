<?php

error_reporting(E_ERROR | E_PARSE);

require dirname(__FILE__).'/bootstrap.php';

$components = array('router' => array('config' => array('module' => array('default-value' => 'default'), 'routes' => array('admin' => array('class' => 'LIB:route.PwAdminRoute', 'default' => true)))));
Wekit::run('pwadmin', $components);
