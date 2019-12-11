<?php
/**
 * @package sycho/populater
 * @author Sami 'SychO' Mazouz
 * @version 1.0.0
 * @license MIT
 */

namespace SychO\Populater\Database;

use Illuminate\Database\Capsule\Manager as Capsule;

class Database
{
    /**
     * @var \Illuminate\Database\Capsule\Manager
     */
    protected static $capsule;

    /**
     * @var array
     */
    const ENV = [
        'CONNECTION' => [
            'name' => 'connection_name',
            'default' => '',
            'required' => true
        ],
        'DB_DRIVER' => [
            'name' => 'driver',
            'default' => 'mysql',
            'required' => true
        ],
        'DB_NAME' => [
            'name' => 'database',
            'default' => '',
            'required' => true
        ],
        'DB_USER' => [
            'name' => 'username',
            'default' => '',
            'required' => true
        ],
        'DB_PASS' => [
            'name' => 'password',
            'default' => '',
            'required' => true
        ],
        'DB_PREFIX' => [
            'name' => 'prefix',
            'default' => '',
            'required' => false
        ],
        'DB_HOST' => [
            'name' => 'host',
            'default' => 'localhost',
            'required' => false
        ],
        'DB_CHARSET' => [
            'name' => 'charset',
            'default' => 'utf8',
            'required' => false
        ],
        'DB_COLLATION' => [
            'name' => 'collation',
            'default' => 'utf8_unicode_ci',
            'required' => false
        ]
    ];

    /**
     * Initializes the database capsule
     */
    public static function initCapsule()
    {
        $conn = [];

        foreach (self::ENV as $var => $info)
            $conn[$info['name']] = env($var, $info['default']);

        self::$capsule = new Capsule();
        self::$capsule->addConnection($conn);
        self::$capsule->setAsGlobal();
        self::$capsule->bootEloquent();
    }

    /**
     *
     */
    public static function __callStatic($method, $args)
    {
        if (!isset(self::$capsule))
            self::initCapsule();

        return Capsule::$method(...$args);
    }
}
