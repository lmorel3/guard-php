<?php

namespace Guard\Controllers;

use Guard\Config;
use Guard\Log;
use Guard\Models\User;
use Guard\Views\View;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Http\Response;

/**
 * Class HomeController
 *
 * @author Laurent Morel
 * @package Guard\Controllers
 */
class HomeController
{

    /**
     * Displays the homepage
     *
     * @param ServerRequestInterface $request
     * @param Response $response
     * @param $args
     * @return int|\Psr\Http\Message\ResponseInterface|Response
     * @throws \Exception
     */
    public function index(ServerRequestInterface $request, Response $response, $args)
    {
        $loginUrl = Config::getGuardUrl() . '/login';

        if(!User::isLogged($request)) {
            return $response->withRedirect($loginUrl);
        }

        $user = User::getConnectedUser($request);
        return View::render($response, 'index', ['username' => $user->username]);
    }

}