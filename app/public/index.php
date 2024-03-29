<?php

/**
 * Guard - Simple and lightweight SSO handler for reverse proxies
 * @author Laurent Morel
 */

use Guard\Config;
use Guard\Log;

require '../vendor/autoload.php';


$file = '/config/database.db';
if(!is_writable($file)) {

    Log::error("Database file '$file' is not writable");
    Log::error("Owner: " . fileowner($file), [ substr(sprintf('%o', fileperms($file)), -4)]);
    Log::error("Current user: " . get_current_user() . " uid:" . getmyuid() . " gid:" . getmygid());
   // header('HTTP/1.0 403 Forbidden');
   // exit;

}

$app = new Slim\App();

/**
 * Handles proxy requests
 */
$app->get('/auth', 'Guard\Controllers\AuthController::handle');

/**
 * Handles login
 */
$app->get('/login', 'Guard\Controllers\UsersController::showLogin');
$app->post('/login', 'Guard\Controllers\UsersController::login');

/**
 * Handles logout
 */
$app->get('/logout', 'Guard\Controllers\UsersController::logout');

/**
 * Displays homepage
 */
$app->get('/', 'Guard\Controllers\HomeController::index');


/**
 * Handles password edition
 */
$app->get('/password', 'Guard\Controllers\UsersController::password');
$app->post('/password', 'Guard\Controllers\UsersController::editPassword');


/**
 * Runs the application
 */
try {
    $app->run();
} catch (\Exception $e) {
    Log::error($e->getMessage());
}