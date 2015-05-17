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

namespace cordillera\middlewares;

use cordillera\base\Cordillera;
use cordillera\base\interfaces\Controller as ControllerIterface;
use cordillera\middlewares\filters\request\Filter;

class Controller implements ControllerIterface
{
    /**
     * @var string
     */
    protected $_method = 'get';

    /**
     * @var array
     */
    protected $_actions = [];

    /**
     * @var string
     */
    protected $_filename;

    /**
     * @var string
     */
    protected $_handler;

    /**
     * @var string html|json
     */
    public $response_type = 'html';

    /**
     * @var Filter
     */
    public $filter;

    /**
     * @var bool
     */
    public $is_rest = false;

    /**
     * @param string $handler
     */
    public function __construct($handler)
    {
        $this->filter = new Filter();
        $this->_handler = $handler;
        $this->_method = strtolower($_SERVER['REQUEST_METHOD']);
        $this->init();
    }

    protected function init()
    {
        $filename_app = CORDILLERA_APP_DIR.'modules'.DS.$this->_handler.'.action.php';
        $filename_cordillera = CORDILLERA_DIR.'modules'.DS.$this->_handler.'.action.php';

        $this->_filename = is_file($filename_app) ?
            $filename_app : (is_file($filename_cordillera) ? $filename_cordillera : '');

        Cordillera::$instance->share('controller', function () {
            return $this;
        });

        if (!empty($this->_filename) && is_file($this->_filename)) {
            require_once $this->_filename;
            $this->run();
        } else {
            throw new Exception(
                Cordillera::app()->lang->translate('The command %s not found', [$this->_handler]),
                404,
                Exception::NOTFOUND
            );
        }
    }

    protected function run()
    {
        $this->filter->assertCsrfToken();

        if (!in_array($this->response_type, ['json', 'html'])) {
            throw new Exception(
                Cordillera::app()->lang->translate('The response type must be json or html'),
                500, Exception::VIEW
            );
        }

        if (isset($this->_actions['filters']) && is_callable($this->_actions['filters'])) {
            $this->_actions['filters']($this);
            $this->filter->execute();
        }
        if (isset($this->_actions[$this->_method]) && is_callable($this->_actions[$this->_method])) {
            $this->_actions[$this->_method]($this);
        } else {
            throw new Exception(Cordillera::app()->lang->translate('HTTP verb is forbidden'), 403, Exception::FORBIDDEN);
        }
    }

    /**
     * @param View|array|string $response
     *
     * @throws Exception
     */
    public function setResponse($response)
    {
        if (!$this->is_rest) {
            Cordillera::app()->response->setSecurityHeaders();
        }

        if ($response instanceof View && $this->response_type == 'html' && !$this->is_rest) {
            Cordillera::app()->response->raw($response->render());
        } elseif (
            ($this->is_rest || $this->response_type == 'json' || is_array($response)) ||
            (is_object($response) && !($response instanceof View))
        ) {
            if ($response instanceof View) {
                throw new Exception(
                    Cordillera::app()->lang->translate('Response can not be a instance of cordillera\\middlewares\\View object'),
                    500,
                    Exception::BADARGUMENTS
                );
            }
            Cordillera::app()->response->json($response);
        }
    }

    /**
     * @param callable $definition
     */
    public function filters(\Closure $definition)
    {
        $this->_actions['filters'] = $definition;
    }

    /**
     * @param callable $definition
     */
    public function put(\Closure $definition)
    {
        $this->_actions['put'] = $definition;
    }

    /**
     * @param callable $definition
     */
    public function get(\Closure $definition)
    {
        $this->_actions['get'] = $definition;
    }

    /**
     * @param callable $definition
     */
    public function post(\Closure $definition)
    {
        $this->_actions['post'] = $definition;
    }

    /**
     * @param callable $definition
     */
    public function delete(\Closure $definition)
    {
        $this->_actions['delete'] = $definition;
    }

    /**
     * @param callable $definition
     */
    public function head(\Closure $definition)
    {
        $this->_actions['head'] = $definition;
    }

    /**
     * @param callable $definition
     */
    public function options(\Closure $definition)
    {
        $this->_actions['options'] = $definition;
    }

    /**
     * @param callable $definition
     */
    public function trace(\Closure $definition)
    {
        $this->_actions['trace'] = $definition;
    }
}
