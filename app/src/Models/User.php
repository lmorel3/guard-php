<?php

namespace Sso\Models;

use Dflydev\FigCookies\FigRequestCookies;
use Dflydev\FigCookies\FigResponseCookies;
use Dflydev\FigCookies\SetCookie;
use Firebase\JWT\JWT;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Http\Response;
use Sso\Config;
use Sso\Log;

/**
 * Class User
 *
 * @package Sso\Models
 * @author Laurent Morel
 */
class User
{

    const TOKEN_KEY = 'token';

    /**
     * Checks for token validity
     *
     * @param ServerRequestInterface $request
     * @return bool
     */
    public static function isLogged(ServerRequestInterface $request): bool {
        $token = FigRequestCookies::get($request, self::TOKEN_KEY, '')->getValue();
        $isValid = true;

        try {
            JWT::decode($token, Config::JWT_KEY, array('HS256'));
        } catch (\Exception $e) {
            return false;
        };

        return $isValid;
    }

    /**
     * Try to login the user with provided credentials
     *
     * If it works, a cookie is set with a generated token
     *
     * @param array $body
     * @param Response $response
     * @return \Psr\Http\Message\ResponseInterface|Response
     */
    public static function login(array $body, Response $response) {
        if(!isset($body['username']) || !isset($body['password'])) {
            return $response;
        }

        if(self::checkCredentials($body['username'], $body['password'])) {

            $token = self::generateToken($body['username']);
            $response = FigResponseCookies::expire($response, 'from_url');

            $response = FigResponseCookies::set($response, SetCookie::create(self::TOKEN_KEY)
                ->withValue($token)
                ->withDomain(Config::DOMAIN)
                ->withPath('/')
            );
        }

        return $response;
    }

    /**
     * Checks if credentials match with a user stored in database
     *
     * @param string $username
     * @param string $password
     * @return bool
     */
    private static function checkCredentials(string $username, string $password): bool {

        $data = Db::get()->select('users', [
            'rowid'
        ], [
            'username' => $username,
            'password' => sha1($password)
        ]);

        Log::debug('Found user', $data);

        return sizeof($data) > 0;

    }

    /**
     * Generates a token for a given user
     *
     * @param string $username
     * @return string
     */
    private static function generateToken(string $username) {
        $token = array(
            'username'      => $username,
            'created_at'    => new \DateTime()
        );

        return JWT::encode($token, Config::JWT_KEY);
    }

}