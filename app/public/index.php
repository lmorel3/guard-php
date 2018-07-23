<?php

/**
 * Guard - Simple and lightweight SSO handler for reverse proxies
 * @author Laurent Morel
 */

use Guard\Log;

require '../vendor/autoload.php';

$app = new Slim\App();

/**
 * Handles proxy requests
 */
$app->get('/auth', 'Guard\Controllers\AuthController::handle');

/**
 * Handles login page display
 */
$app->get('/login', 'Guard\Controllers\UsersController::showLogin');

/**
 * Handles login attempts
 */
$app->post('/login', 'Guard\Controllers\UsersController::login');

/**
 * Runs the application
 */
try {
    $app->run();
} catch (\Exception $e) {
    Log::error($e->getMessage());
}