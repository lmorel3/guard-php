<?php

namespace Guard;

use Symfony\Component\Yaml\Yaml;

/**
 * Class Config
 *
 * @package Guard
 * @author Laurent Morel
 */
class Config
{

    private static $instance = null;

    /**
     * Return (or create) a singleton of Config
     * @return Config
     */
    public static function getInstance(): Config {
        if(self::$instance === null) {
            self::$instance = new Config();
        }

        return self::$instance;
    }

    /**
     * Get config value
     *
     * @param string $key
     * @param string $rootKey
     * @return mixed
     */
    public static function get(string $key, string $rootKey = 'app') {
        return Config::getInstance()->getValue($key, $rootKey);
    }

    /**
     * Return app url with the right protocol
     *
     * @return string
     */
    public static function getGuardUrl() {
        $useSsl = (bool) Config::getInstance()->get('useSsl');
        $authUrl = Config::getInstance()->get('authUrl');

        $protocol = ($useSsl) ? 'https' : 'http';

        return "$protocol://$authUrl";
    }

    //////////

    /**
     * Contains configuration loaded from config file
     * @var array
     */
    private $config = [];

    /**
     * Required keys
     * @var array
     */
    private static $keys = [
        'app' => [
            'authUrl',
            'domain',
            'useSsl',
            'jwtKey',
            'cookieDuration'
        ],
        'log' => [
            'file',
            'level'
        ],
        'db' => [
            'file'
        ]
    ];

    /**
     * Config constructor.
     *
     * @throws \RuntimeException
     */
    public function __construct()
    {
        $env = getenv('CONF_FILE');
        $file = $env ? $env : '../config.yaml';

        $this->config = Yaml::parseFile($file);
        $this->checkConfig();
    }

    /**
     * Get config value
     * @param string $key
     * @param string $rootKey
     * @return mixed
     */
    public function getValue(string $key, string $rootKey) {
        return $this->config[$rootKey][$key];
    }

    /**
     * Check if required config keys are available
     *
     * @throws \RuntimeException
     */
    private function checkConfig() {

        foreach (self::$keys as $k => $subKeys) {

            if(!isset($this->config[$k]))
            {
                throw new \RuntimeException('Missing root key : ' . $k);
            }

            foreach ($subKeys as $s) {
                if(!isset($this->config[$k][$s]))
                {
                    throw new \RuntimeException('Missing sub key : ' . $k . '.' . $s);
                }
            }
        }

    }

}