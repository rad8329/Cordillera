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

namespace cordillera\base\interfaces;

use cordillera\middlewares\Auth;
use cordillera\middlewares\Config;
use cordillera\middlewares\Controller;
use cordillera\middlewares\db\Connection;
use cordillera\middlewares\Lang;
use cordillera\middlewares\Request;
use cordillera\middlewares\Router;
use cordillera\middlewares\Session;

interface Application
{
    /**
     * @return Connection
     */
    public static function getDb();

    /**
     * @return Request
     */
    public static function getRequest();

    /**
     * @return Auth
     */
    public static function getAuth();

    /**
     * @return Session
     */
    public static function getSession();

    /**
     * @return Router
     */
    public static function getRouter();

    /**
     * @return Lang
     */
    public static function getLang();

    /**
     * @return Config
     */
    public static function getConfig();

    /**
     * @return Controller
     */
    public static function getController();

    public static function halt();
}