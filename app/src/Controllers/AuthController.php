<?php

namespace Guard\Controllers;

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
    public function handle(ServerRequestInterface $request, Response $response, $args)
    {
        Log::info('Handling /auth');

        $ssoReq = new SsoRequest($request, true);
        Log::debug($ssoReq);

        // Request from this app
        if($ssoReq->isAuth()) {
            return $response;
        }

        // Not logged : redirects to login page
        if(!User::isLogged($request)) {
            return $ssoReq->updateResponse($response)
                          ->withRedirect(\Guard\Config::LOGIN_URL);
        }

        // User is logged => 200
        return $response->withStatus(204);
    }
}