<?php

namespace Guard\Controllers;

use Guard\Config;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Http\Response;
use Guard\Log;
use Guard\Models\SsoRequest;
use Guard\Models\User;

/**
 * Class AuthController
 *
 * @package Guard\Controllers
 * @author Laurent Morel
 */
class AuthController
{
    private $container;

    public function __construct($container)
    {
        $this->container = $container;
    }

    /**
     * Handles authentication for the proxy
     *
     * @param ServerRequestInterface $request
     * @param Response $response
     * @param $args
     * @return \Psr\Http\Message\ResponseInterface|Response
     */
    public function handle(ServerRequestInterface $request, Response $response)
    {
        Log::info('Handling /auth', [$request]);

        $ssoReq = new SsoRequest($request, true);
        Log::debug($ssoReq);

        // Request from this app
        if($ssoReq->isAuth()) {
            return $response;
        }

        // Not logged : redirects to login page
        if(!User::isLogged($request)) {
            $loginUrl = Config::getGuardUrl() . '/login';

            return $ssoReq->updateResponse($response)
                          ->withRedirect($loginUrl);
        }

        // User is logged => 200
        return $response->withStatus(204);
    }
}