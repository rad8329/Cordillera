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

use cordillera\base\Application;
use cordillera\base\Cordillera;
use cordillera\helpers\Crypt;
use cordillera\base\interfaces\Controller as ControllerIterface;

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
    public $type = 'html';

    /**
     * @var bool
     */
    public $rest = false;

    /**
     * @param string $handler
     */
    public function __construct($handler)
    {
        $this->_handler = $handler;
        $this->_method = strtolower($_SERVER['REQUEST_METHOD']);
        $this->init();
    }

    protected function init()
    {
        $filename_app = CORDILLERA_APP_DIR . 'modules' . DS . $this->_handler . '.action.php';
        $filename_cordillera = CORDILLERA_DIR . 'modules' . DS . $this->_handler . '.action.php';

        $this->_filename = is_file($filename_app) ?
            $filename_app : (is_file($filename_cordillera) ? $filename_cordillera : '');

        Cordillera::$instance->share("controller", function () {
            return $this;
        });

        if (!empty($this->_filename) && is_file($this->_filename)) {

            require_once $this->_filename;
            $this->run();
        } else {
            throw new Exception(
                Application::getLang()->translate("The command %s not found", [$this->_handler]),
                404,
                Exception::NOTFOUND
            );
        }
    }

    protected function run()
    {
        $this->assertCsrfToken();

        if (!in_array($this->type, ['json', 'html'])) {
            throw new Exception(
                Application::getLang()->translate("The response type must be json or html"),
                500, Exception::VIEW
            );
        }

        if (isset($this->_actions['filters']) && is_callable($this->_actions['filters'])) {
            $this->_actions['filters']($this);
        }
        if (isset($this->_actions[$this->_method]) && is_callable($this->_actions[$this->_method])) {
            $this->_actions[$this->_method]($this);
        } else {
            throw new Exception(Application::getLang()->translate("HTTP verb is forbidden"), 403, Exception::FORBIDDEN);
        }
    }

    /**
     * @param View|array|string $response
     * @throws Exception
     */
    public function setResponse($response)
    {
        if (!$this->rest) {
            Response::setSecurityHeaders();
        }

        if ($response instanceof View && $this->type == 'html' && !$this->rest) {
            Response::raw($response->render());
        } elseif (
            ($this->rest || $this->type == 'json' || is_array($response)) ||
            (is_object($response) && !($response instanceof View))
        ) {
            if ($response instanceof View) {
                throw new Exception(
                    Application::getLang()->translate("Response can not be a instance of cordillera\\middlewares\\View object"),
                    500,
                    Exception::BADARGUMENTS
                );
            }
            Response::json($response);
        }
    }


    /**
     * @throws Exception
     */
    public function assertJsonContentType()
    {
        if (
            !isset($_SERVER["CONTENT_TYPE"]) ||
            (isset($_SERVER["CONTENT_TYPE"]) && !preg_match('/^application\/json/', $_SERVER["CONTENT_TYPE"]))
        ) {
            throw new Exception(Application::getLang()->translate("Bad request"), 400, Exception::BADREQUEST);
        }
    }

    /**
     * @throws Exception
     */
    public function assertCsrfToken()
    {
        if (Application::getConfig()->get("request.csrf") && Request::isPost() && !$this->rest) {
            // If the CSRF token is enabled, and is post method request
            $request = Application::getRequest();
            $payload = Request::payload(Application::getRequest()->csrf_id);
            $post = Request::post(Application::getRequest()->csrf_id);

            if (
                // POST data
                (empty($payload) && $post != $request->csrf_value) ||
                // Payload data
                (!empty($payload) && $payload != $request->csrf_value)
            ) {
                throw new Exception(Application::getLang()->translate("Bad request"), 400, Exception::BADREQUEST);
            }
        }
    }

    /**
     * @throws Exception
     */
    public function assertAjax()
    {
        if (!Request::isAjax()) {
            throw new Exception(Application::getLang()->translate("Bad request"), 400, Exception::BADREQUEST);
        }
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
    public function filters(\Closure $definition)
    {
        $this->_actions['filters'] = $definition;
    }
}