<?php

namespace Guard\Models;

use Dflydev\FigCookies\FigRequestCookies;
use Dflydev\FigCookies\FigResponseCookies;
use Dflydev\FigCookies\SetCookie;
use Firebase\JWT\JWT;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Http\Response;
use Guard\Config;
use Guard\Log;

/**
 * Class User
 *
 * @package Guard\Models
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
            JWT::decode($token, Config::get('jwtKey'), array('HS256'));
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

        $users = self::fetchUser($body['username'], $body['password']);
        if(sizeof($users) > 0) {
            $user = $users[0];

            $token = self::generateToken($user);
            $response = FigResponseCookies::set($response, SetCookie::create('from_url')
                ->withExpires(strtotime('0'))
                ->withDomain(Config::get('domain'))
                ->withPath('/')
            );
            $response = FigResponseCookies::set($response, SetCookie::create(self::TOKEN_KEY)
                ->withValue($token)
                ->withExpires(strtotime('+' . Config::get('cookieDuration') . ' days'))
                ->withDomain(Config::get('domain'))
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
     * @return array
     */
    private static function fetchUser(string $username, string $password): array {

        $data = Db::get()->select('users', [
            'rowid', 'username', 'role'
        ], [
            'username' => $username,
            'password' => sha1($password)
        ]);

        Log::debug('Found user', $data);

        return $data;

    }

    /**
     * Generates a token for a given user
     *
     * @param string $user
     * @return string
     */
    private static function generateToken(array $user) {
        $token = array(
            'username'      => $user['username'],
            'role'          => $user['role'],
            'created_at'    => new \DateTime()
        );

        return JWT::encode($token, Config::get('jwtKey'));
    }

}