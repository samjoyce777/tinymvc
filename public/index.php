<?php

/* * * error reporting on ** */
error_reporting(E_ALL);

/* * * define the site path ** */
$site_path = realpath(dirname(__FILE__));
$back_path = str_replace("\public", "", $site_path);
define('__SITE_PATH', $back_path);



/* * * include the config.php file ** */
require __SITE_PATH . '/includes/config.php';

/* * * include the init.php file ** */
require __SITE_PATH . '/includes/init.php';



/* * * create the database class ** */
try {
    $db = new PDO(DATABASE, DB_USER, DB_PASSWORD);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo $e->getMessage();
}


/* * * load the router ** */
$registry->router = new router($registry);

/* * * set the controller path ** */
$registry->router->setPath(__SITE_PATH . '/controller');

/* * * load up the template ** */
$registry->template = new template($registry);

/* * * load the controller ** */
$registry->router->loader();
