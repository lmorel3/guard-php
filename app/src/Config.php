<?php

namespace Guard;

/**
 * Class Config
 *
 * @package Guard
 * @author Laurent Morel
 */
class Config
{

    /**
     * Auth domain's url
     * e.g. 'auth.guard.local'
     */
    const AUTH_URL  = 'auth.guard.local';

    /**
     * Domain (without protocol
     * e.g. 'guard.local'
     */
    const DOMAIN    = 'guard.local';

    /**
     * Private key used to generate and verify JWT tokens
     *
     * NB: Use a STRONG key and keep it secured
     */
    const JWT_KEY   = 'mAcJ)hqCB93aL5sx<+#k';

    /**
     * Url where users are redirected to login
     * e.g. 'http://auth.guard.local/login'
     *
     * NB: Should contain `AUTH_URL`, otherwise it'll generate an infinite loop
     */
    const LOGIN_URL = 'http://auth.guard.local/login';

}