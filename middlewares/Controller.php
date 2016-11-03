<?php

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
     * @var Router
     */
    public $router;

    /**
     * @var Request
     */
    public $request;

    /**
     * @var Response
     */
    public $response;

    /**
     * @param Router $router
     */
    public function __construct(Router $router)
    {
        $this->router = $router;
        $this->request = app()->request;
        $this->response = app()->response;
        $this->filter = new Filter();
        $this->_method = strtolower($this->request->getMethod());
        $this->init();
    }

    protected function init()
    {
        $filename_app = CORDILLERA_APP_DIR.'modules'.DS.$this->router->handler.'.action.php';
        $filename_cordillera = CORDILLERA_DIR.'modules'.DS.$this->router->handler.'.action.php';

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
                translate('The command %s not found', [$this->router->handler]),
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
                translate('The response type must be json or html'),
                500,
                Exception::VIEW
            );
        }

        if (isset($this->_actions['filters']) && is_callable($this->_actions['filters'])) {
            $this->_actions['filters']($this);
            $this->filter->execute();
        }
        if (isset($this->_actions[$this->_method]) && is_callable($this->_actions[$this->_method])) {
            $this->_actions[$this->_method]($this);
        } else {
            throw new Exception(translate('HTTP verb is forbidden'), 403, Exception::FORBIDDEN);
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
            $this->response->setSecurityHeaders();
        }

        if ($response instanceof View && $this->response_type == 'html' && !$this->is_rest) {
            $this->response->raw($response->render());
        } elseif (($this->is_rest || $this->response_type == 'json' || is_array($response)) ||
            (is_object($response) && !($response instanceof View))
        ) {
            if ($response instanceof View) {
                throw new Exception(
                    translate('Response can not be a instance of cordillera\\middlewares\\View object'),
                    500,
                    Exception::BADARGUMENTS
                );
            }
            $this->response->json($response);
        }
    }

    /**
     * @param string $url
     */
    public function redirect($url)
    {
        $this->response->setHeader('Location', $url);
        exit;
    }

    /**
     * @param callable|\Closure $definition
     */
    public function filters(\Closure $definition)
    {
        $this->_actions['filters'] = $definition;
    }

    /**
     * @param callable|\Closure $definition
     */
    public function put(\Closure $definition)
    {
        $this->_actions['put'] = $definition;
    }

    /**
     * @param callable|\Closure $definition
     */
    public function get(\Closure $definition)
    {
        $this->_actions['get'] = $definition;
    }

    /**
     * @param callable|\Closure $definition
     */
    public function post(\Closure $definition)
    {
        $this->_actions['post'] = $definition;
    }

    /**
     * @param callable|\Closure $definition
     */
    public function delete(\Closure $definition)
    {
        $this->_actions['delete'] = $definition;
    }

    /**
     * @param callable|\Closure $definition
     */
    public function head(\Closure $definition)
    {
        $this->_actions['head'] = $definition;
    }

    /**
     * @param callable|\Closure $definition
     */
    public function options(\Closure $definition)
    {
        $this->_actions['options'] = $definition;
    }

    /**
     * @param callable|\Closure $definition
     */
    public function trace(\Closure $definition)
    {
        $this->_actions['trace'] = $definition;
    }
}
