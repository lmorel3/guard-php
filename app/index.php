<?php

/**
 * Guard - Simple and lightweight SSO handler for reverse proxies
 * @author Laurent Morel
 */

use Sso\Log;

require __DIR__ . '/vendor/autoload.php';

$app = new Slim\App();

/**
 * Handles proxy requests
 */
$app->get('/auth', 'Sso\Controllers\AuthController::handle');

/**
 * Handles login page display
 */
$app->get('/login', 'Sso\Controllers\UsersController::showLogin');

/**
 * Handles login attempts
 */
$app->post('/login', 'Sso\Controllers\UsersController::login');

/**
 * Runs the application
 */
try {
    $app->run();
} catch (\Exception $e) {
    Log::error($e->getMessage());
}