<?php

namespace cordillera\base;

use cordillera\middlewares\Exception;

class Cordillera
{
    /**
     * @var Application
     */
    private static $_app;

    /**
     * @var string
     */
    public static $exception;

    /**
     * @var DI
     */
    public static $instance;

    /**
     * @var array
     */
    public static $definitions = [];

    /**
     * @param string $classname
     * @param array  $config
     * @param string $alternative_classname
     *
     * @return mixed
     */
    public static function factory($classname, array $config = [], $alternative_classname = '')
    {
        $object = null;
        if (class_exists($classname)) {
            $reflect = new \ReflectionClass($classname);

            $object = $reflect->newInstanceArgs($config);
        } else {
            self::$exception = new Exception(
                translate('%s not found', [$classname]),
                500,
                Exception::ERROR
            );
        }

        if (!class_exists($classname) && class_exists($alternative_classname)) {
            $reflect = new \ReflectionClass($alternative_classname);

            $object = $reflect->newInstanceArgs($config);
        }

        return $object;
    }

    /**
     * @return Application
     */
    public static function app()
    {
        if (!self::$_app) {
            self::$_app = new Application();
        }

        return self::$_app;
    }
}
