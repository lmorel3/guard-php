<?php

namespace Sso\Handlers;

/**
 * Class TraefikHandler
 *
 * @package Sso\Handlers
 * @author Laurent Morel
 */
class TraefikHandler implements IHandler
{

    /**
     * @var \Psr\Http\Message\ServerRequestInterface
     */
    private $request;

    public function __construct(\Psr\Http\Message\ServerRequestInterface $request)
    {
        $this->request = $request;
    }

    public function getName(): string
    {
        return 'Traefik';
    }

    public function getRedirectUrl(): string
    {
        $redirectUrl = '';
        //$this->ip = $_SERVER["HTTP_X_FORWARDED_FOR"];

        if(isset($_SERVER["HTTP_X_FORWARDED_PROTO"])) {
            $uri = isset($_SERVER['HTTP_X_FORWARDED_URI']) ? $_SERVER['HTTP_X_FORWARDED_URI'] : '';
            $redirectUrl = $_SERVER["HTTP_X_FORWARDED_PROTO"] . "://" . $_SERVER["HTTP_X_FORWARDED_HOST"] . $uri;
        }

        return $redirectUrl;
    }

}