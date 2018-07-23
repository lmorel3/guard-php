<?php

namespace Sso\Models;

use Medoo\Medoo;

/**
 * Class Db
 *
 * @package Sso\Models
 * @author Laurent Morel
 */
class Db
{

    /**
     * @var Medoo
     */
    private static $db;

    /**
     * Returns a singleton of the database
     *
     * @return Medoo
     */
    public static function get(): Medoo {

        if(Db::$db !== null) {
            return Db::$db;
        }

        Db::$db = new Medoo([
            'database_type' => 'sqlite',
            'database_file' => '/database.sqlite'
        ]);

        return Db::$db;

    }

}