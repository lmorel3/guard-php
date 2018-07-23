<?php

namespace Sso\Models;

use Dflydev\FigCookies\FigRequestCookies;
use Dflydev\FigCookies\FigResponseCookies;
use Dflydev\FigCookies\SetCookie;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Http\Response;
use Sso\Config;
use Sso\Handlers\TraefikHandler;
use Sso\Log;

/**
 * Class SsoRequest
 *
 * @package Sso\Models
 * @author Laurent Morel
 */
class SsoRequest
{

    private $token;
    private $hasToken = false;

    private $setCookie = false;

    /**
     * URL used for redirecting user after login
     * @var string
     */
    private $requestUrl = '';

    public function __construct(ServerRequestInterface $request, bool $setCookie = false)
    {

        $this->requestUrl = FigRequestCookies::get($request, 'from_url', '')->getValue();
        $handler = new TraefikHandler($request); // TODO: Generify

        // First request
        if($setCookie) {
            $this->setCookie = true;
            $this->requestUrl = $handler->getRedirectUrl();

            Log::info('Handling request with ' . $handler->getName() . ' handler');
            Log::debug('=> ' . $this->requestUrl);
        }

        $this->token = FigRequestCookies::get($request, 'token', '')->getValue();
        $this->hasToken = $this->token !== '';

    }

    /**
     * Checks if the request is part of the Guard app's domain
     * @return bool
     */
    public function isAuth() {
        $parsed = parse_url($this->requestUrl);

        return isset($parsed['host']) && $parsed['host'] === Config::AUTH_URL;
    }

    /**
     * @return mixed
     */
    public function getToken(): string
    {
        return $this->token;
    }

    /**
     * @return bool
     */
    public function hasToken(): bool
    {
        return $this->hasToken;
    }

    /**
     * @return string
     */
    public function getRequestUrl(): string
    {
        return $this->requestUrl;
    }

    /**
     * Sets the cookie if needed and returns the Response
     *
     * @param Response $response
     * @return \Psr\Http\Message\ResponseInterface|Response
     */
    public function updateResponse(Response $response) {

        if($this->setCookie) {
            return FigResponseCookies::set($response, SetCookie::create('from_url')
                ->withValue($this->requestUrl)
                ->withDomain(Config::DOMAIN)
                ->withPath('/')
            );
        }

        return $response;

    }

    public function __toString()
    {
        return
            'Request: ' . $this->requestUrl.
            'Token: ' . $this->token;
    }


}