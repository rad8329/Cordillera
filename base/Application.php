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
use cordillera\middlewares\Logger;
use cordillera\middlewares\Request;
use cordillera\middlewares\Response;
use cordillera\middlewares\Router;
use cordillera\middlewares\Session;
use cordillera\base\interfaces\Application as ApplicationInterface;

/**
 * @property Config $config
 * @property Controller $controller
 * @property Connection $db
 * @property Auth $auth
 * @property Lang $lang
 * @property Logger $logger
 * @property Request $request
 * @property Response $response
 * @property Router $router
 * @property Session $session
 */
class Application implements ApplicationInterface
{
    /**
     * @var Request
     */
    protected $request;

    /**
     * @var Response
     */
    protected $response;

    /**
     * @var Connection
     */
    protected $db;

    /**
     * @var Logger
     */
    protected $logger;

    /**
     * @var Auth
     */
    protected $auth;

    /**
     * @var Session
     */
    protected $session;

    /**
     * @var Router
     */
    protected $router;

    /**
     * @var Lang
     */
    protected $lang;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var Controller
     */
    protected $controller;

    /**
     * @param string $component
     *
     * @return mixed
     */
    public function __get($component)
    {
        return Cordillera::$instance->{$component};
    }

    public function halt()
    {
        Cordillera::$instance->destroy('db');
    }
}
