<?php

namespace Sso\Controllers;

use Dflydev\FigCookies\FigResponseCookies;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Http\Response;
use Sso\Log;
use Sso\Models\SsoRequest;
use Sso\Models\User;

/**
 * Class UsersController
 *
 * @package Sso\Controllers
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
    public function showLogin(ServerRequestInterface $request, Response $response, $args)
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
        $content = file_get_contents('./tpl/login.html');
        return $ssoReq->updateResponse($response)
                       ->getBody()->write($content);

    }

    /**
     * Handles login attempts
     *
     * @param ServerRequestInterface $request
     * @param Response $response
     * @param $args
     * @return \Psr\Http\Message\ResponseInterface|Response
     */
    public function login(ServerRequestInterface $request, Response $response, $args)
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

        // In case of bad credentials, user is redirected to login page
        $redirectUrl = FigResponseCookies::get($response, User::TOKEN_KEY, '')->getValue() == ''
            ? \Sso\Config::LOGIN_URL
            : $ssoReq->getRequestUrl();

        return $ssoReq->updateResponse($response)
                      ->withRedirect($redirectUrl);
    }

}