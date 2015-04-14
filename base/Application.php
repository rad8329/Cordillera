<?php

/*
 * This file is part of the Cordillera framework.
 *
 * (c) Robert Adrián Díaz <rad8329@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Made with love in Medellín
 */

namespace cordillera\base;

use cordillera\middlewares\Config;
use cordillera\middlewares\Controller;
use cordillera\middlewares\db\Connection;
use cordillera\middlewares\Auth;
use cordillera\middlewares\Lang;
use cordillera\middlewares\Request;
use cordillera\middlewares\Router;
use cordillera\middlewares\Session;
use cordillera\base\interfaces\Application as ApplicationInterface;

class Application implements ApplicationInterface
{
    /**
     * @return Connection
     */
    public static function getDb()
    {
        return Cordillera::get('db');
    }

    /**
     * @return Request
     */
    public static function getRequest()
    {
        return Cordillera::get('request');
    }

    /**
     * @return Auth
     */
    public static function getAuth()
    {
        return Cordillera::get('auth');
    }

    /**
     * @return Session
     */
    public static function getSession()
    {
        return Cordillera::get('session');
    }

    /**
     * @return Router
     */
    public static function getRouter()
    {
        return Cordillera::get('router');
    }

    /**
     * @return Lang
     */
    public static function getLang()
    {
        return Cordillera::get('lang');
    }

    /**
     * @return Config
     */
    public static function getConfig()
    {
        return Cordillera::get('config');
    }

    /**
     * @return Controller
     */
    public static function getController()
    {
        return Cordillera::get('controller');
    }

    public static function halt()
    {
        Cordillera::$instance->destroy('db');
    }
}
