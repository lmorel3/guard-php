<?php

namespace Sso;


use Exception;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

/**
 * Class Log
 * Wraps a Monolog\Logger with a singleton
 *
 * @package Sso
 * @author Laurent Morel
 */
class Log
{

    private static $logger = null;

    public static function debug($msg, $context = []) {
        self::getInstance()->debug($msg, $context);
    }

    public static function info($msg, $context = []) {
        self::getInstance()->info($msg, $context);
    }

    public static function error($msg, $context = []) {
        self::getInstance()->error($msg, $context);
    }

    /**
     * Returns a singleton of the logger
     * @return Logger|null
     */
    private static function getInstance() {

        if(self::$logger !== null) {
            return self::$logger;
        }

        try {
            self::$logger = new Logger('guard');
            self::$logger->pushHandler(new StreamHandler('php://stderr', Logger::DEBUG));
        } catch (Exception $e) {
            error_log("Unable to create logger");
            die();
        }

        return self::$logger;

    }

}