<?php

namespace Guard\Controllers;

use Dflydev\FigCookies\FigResponseCookies;
use Guard\Config;
use Guard\Views\View;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Http\Response;
use Guard\Log;
use Guard\Models\SsoRequest;
use Guard\Models\User;

/**
 * Class UsersController
 *
 * @package Guard\Controllers
 * @author Laurent Morel
 */
class UsersController
{

    private $container;

    public function __construct($container)
    {
        $this->container = $container;
    }

    /**
     * Displays the login form
     *
     * @param ServerRequestInterface $request
     * @param Response $response
     * @param $args
     * @return int|\Psr\Http\Message\ResponseInterface|Response
     */
    public function showLogin(ServerRequestInterface $request, Response $response)
    {
        Log::info('Handling GET /login');

        $ssoReq = new SsoRequest($request);
        Log::debug($ssoReq);

        // Already logged
        if(User::isLogged($request)) {
            Log::debug('Request headers are ', $request->getHeaders());
            return $ssoReq->updateResponse($response)
                          ->withRedirect($ssoReq->getRequestUrl());
        }

        // Displays login page
        return View::render($ssoReq->updateResponse($response), 'login', ['displayError' => false ? 'block' : 'none']);
    }

    /**
     * Handles login attempts
     *
     * @param ServerRequestInterface $request
     * @param Response $response
     * @param $args
     * @return \Psr\Http\Message\ResponseInterface|Response
     */
    public function login(ServerRequestInterface $request, Response $response)
    {
        Log::info('Handling POST /login');

        $ssoReq = new SsoRequest($request);
        Log::debug($ssoReq);

        // Already logged
        if(User::isLogged($request)) {
            return $ssoReq->updateResponse($response)->withRedirect($ssoReq->getRequestUrl());
        }

        // Tries to connect user
        $parsedBody = $request->getParsedBody();
        Log::info('Attempt to connect : ' . $parsedBody['username']);
        $response = User::login($parsedBody, $response);

        // In case of bad credentials, user must retry login
        $redirectUrl = FigResponseCookies::get($response, User::TOKEN_KEY, '')->getValue();
        if($redirectUrl === '') {
            return View::render($ssoReq->updateResponse($response), 'login', ['displayError' => true ? 'block' : 'none']);
        }

        return $ssoReq->updateResponse($response)
                      ->withRedirect($redirectUrl);
    }

    /**
     * Logs out a user
     *
     * @param ServerRequestInterface $request
     * @param Response $response
     * @return \Psr\Http\Message\ResponseInterface|Response
     */
    public function logout(ServerRequestInterface $request, Response $response) {
        $loginUrl = Config::getGuardUrl() . '/login';

        if(User::isLogged($request)) {
            $response = User::logout($response);
        }

        return $response->withRedirect($loginUrl);
    }

    /**
     * Displays the password editing page
     *
     * @param ServerRequestInterface $request
     * @param Response $response
     * @param $args
     * @return int|\Psr\Http\Message\ResponseInterface|Response
     * @throws \Exception
     */
    public function password(ServerRequestInterface $request, Response $response)
    {
        $loginUrl = Config::getGuardUrl() . '/login';

        if(!User::isLogged($request)) {
            return $response->withRedirect($loginUrl);
        }

        return View::render($response, 'edit_password', ['displaySuccess' => 'none']);
    }

    /**
     * Handle password editing
     *
     * @param ServerRequestInterface $request
     * @param Response $response
     * @param $args
     * @return int|\Psr\Http\Message\ResponseInterface|Response
     * @throws \Exception
     */
    public function editPassword(ServerRequestInterface $request, Response $response, $args)
    {
        $loginUrl = Config::getGuardUrl() . '/login';

        if(!User::isLogged($request)) {
            return $response->withRedirect($loginUrl);
        }

        $parsedBody = $request->getParsedBody();
        $success = User::editPassword($request, $parsedBody);

        return View::render($response, 'edit_password', ['displaySuccess' => $success ? 'block' : 'none']);
    }

}