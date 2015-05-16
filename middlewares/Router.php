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
use cordillera\base\interfaces\Router as RouterInterface;

class Router implements RouterInterface
{
    /**
     * @var string
     */
    protected $_default_route;

    /**
     * @var string
     */
    protected $_script_name;

    /**
     * @var bool
     */
    protected $_show_index_file;

    /**
     * @var array
     */
    protected $_routes = [];

    /**
     * @var string
     */
    protected $_base_path;

    /**
     * @var string
     */
    protected $_controller_classname;

    /**
     * @var array
     */
    protected $_match_types = [
        'i' => '[0-9]++',
        'a' => '[0-9A-Za-z]++',
        'h' => '[0-9A-Fa-f]++',
        '*' => '.+?',
        '**' => '.++',
        '' => '[^/\.]++',
    ];

    /**
     * @var array
     */
    protected $_handlers = [];

    /**
     * @param array  $rules
     * @param string $base_path
     * @param string $script_name
     * @param string $controller_classname
     * @param string $default_route
     * @param bool   $show_index_file
     * @param array  $match_types
     */
    public function __construct(
        $base_path,
        $script_name,
        $default_route,
        $controller_classname,
        array $rules = [],
        $show_index_file = true,
        array $match_types = []
    ) {
        $this->_script_name = $script_name;
        $this->_base_path = $base_path;
        $this->_match_types = array_merge($this->_match_types, $match_types);
        $this->_default_route = $default_route;
        $this->_show_index_file = $show_index_file;
        $this->_controller_classname = $controller_classname;
        $this->init($rules);
    }

    protected function init(array $rules)
    {
        /*
         * @TODO: Allow to add multiples routes to the same controller action
         */
        $this->add($rules);
    }

    /**
     * Add multiple routes at once from array in the following format:.
     *
     *   $routes = [
     *      a[$route, $handler]
     *   ];
     *
     * @param array $routes
     *
     * @throws Exception
     */
    public function add(array $routes)
    {
        foreach ($routes as $route) {
            call_user_func_array([$this, 'map'], $route);
        }
    }

    /**
     * Map a route to a target.
     *
     * @param string $route   The route regex, custom regex must start with an @.
     *                        You can use multiple pre-set regex filters, like [i:id]
     * @param mixed  $handler The handler target where this route should point to.
     *
     * @throws Exception
     */
    public function map($route, $handler)
    {
        $this->_routes[] = [$route, $handler];

        if ($handler) {
            if (isset($this->_handlers[$handler])) {
                throw new Exception(Application::getLang()->translate('Can not redeclare route %s', [$handler]), 500, Exception::ROUTING);
            } else {
                $this->_handlers[$handler] = $route;
            }
        }
    }

    /**
     * Reversed routing.
     *
     * Generate the URL for a named route. Replace regexes with supplied parameters
     *
     * @param string $route The name of the route.
     * @param array @params Associative array of parameters to replace placeholders with.
     *
     * @return string The URL of the route with named parameters in place.
     */
    public function generate($route, array $params = [])
    {
        if (!isset($this->_handlers[$route])) {
            $url = $this->_show_index_file ? '/'.$this->_script_name.'/'.$route : '/'.$route;

            return $url.(!empty($params) ? '?'.http_build_query($params, '&') : '');
        }

        $route = $this->_handlers[$route];
        //prepare url
        $url = $this->_show_index_file ? '/'.$this->_script_name.$route : $route;

        if (preg_match_all('`(/|\.|)\[([^:\]]*+)(?::([^:\]]*+))?\](\?|)`', $route, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                list($block, $pre, $type, $param, $optional) = $match;
                unset($type);

                if ($pre) {
                    $block = substr($block, 1);
                }
                if (isset($params[$param])) {
                    $url = str_replace($block, $params[$param], $url);
                    unset($params[$param]);
                } elseif ($optional) {
                    $url = str_replace($pre.$block, '', $url);
                } else {
                    $url = str_replace($pre.$block, '', $url);
                }
            }
            $url = $url.(!empty($params) ? '?'.http_build_query($params, '&') : '');
        }

        return $url;
    }

    /**
     * Match a given Request Url against stored routes.
     *
     * @param string $request_url
     *
     * @return array|bool Array with route information on success, false on failure (no match).
     */
    public function match($request_url = null)
    {
        $params = [];
        $request = $_GET;

        if (isset($request['h']) && $request_url == null) {
            $request_url = $request['h'];
            unset($request['h']);
        } else {

            // set Request Url if it isn't passed as parameter
            if ($request_url === null) {
                $request_url = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '/';
            }

            // strip base path from request url
            if (($strpos = strpos($request_url, $this->_script_name)) !== false) {
                $_tmp_request = explode($this->_script_name, $request_url);
                $request_url = ltrim($_tmp_request[1], '/');
                unset($_tmp_request);
            } else {
                $request_url = substr($request_url, strlen($this->_base_path));
            }

            // Strip query string (?a=b) from Request Url
            if (($strpos = strpos($request_url, '?')) !== false) {
                $request_url = substr($request_url, 0, $strpos);
            }
        }

        $request_url = '/'.$request_url;

        foreach ($this->_routes as $handler) {
            list($_route, $target) = $handler;
            // Check for a wildcard (matches all)
            if ($_route === '*') {
                $match = true;
            } elseif (isset($_route[0]) && $_route[0] === '@') {
                $pattern = '`'.substr($_route, 1).'`u';
                $match = preg_match($pattern, $request_url, $params);
            } else {
                $route = null;
                $regex = false;
                $j = 0;
                $n = isset($_route[0]) ? $_route[0] : null;
                $i = 0;

                // Find the longest non-regex substring and match it against the URI
                while (true) {
                    if (!isset($_route[$i])) {
                        break;
                    } elseif (false === $regex) {
                        $c = $n;
                        $regex = $c === '[' || $c === '(' || $c === '.';
                        if (false === $regex && false !== isset($_route[$i + 1])) {
                            $n = $_route[$i + 1];
                            $regex = $n === '?' || $n === '+' || $n === '*' || $n === '{';
                        }
                        if (false === $regex && $c !== '/' && (!isset($request_url[$j]) || $c !== $request_url[$j])) {
                            continue 2;
                        }
                        $j++;
                    }
                    $route .= $_route[$i++];
                }

                $regex = $this->compile($route);
                $match = preg_match($regex, $request_url, $params);
            }

            if (($match == true || $match > 0)) {
                if ($params) {
                    foreach ($params as $key => $value) {
                        if (is_numeric($key)) {
                            unset($params[$key]);
                        }
                    }
                }

                return [
                    'friendly' => true,
                    'handler' => $target,
                    'params' => $params,
                ];
            }
        }

        return $request_url;
    }

    /**
     * @return Controller
     *
     * @throws Exception
     */
    public function dispatch()
    {
        $match = $this->match();

        if (class_exists($this->_controller_classname)) {
            $controller = $this->_controller_classname;
        } else {
            Cordillera::$exception = new Exception(
                Application::getLang()->translate('%s not found', [$this->_controller_classname]),
                500,
                Exception::ERROR
            );
            $controller = 'cordillera\\middlewares\\Controller';
        }

        $handler = '';

        if (isset($match['friendly']) && !$match['friendly']) {
            $handler = $match['handler'] ? $match['handler'] : $this->_default_route;
        } elseif (isset($match['friendly']) && $match['friendly']) {
            $handler = $match['handler'];
            $_GET = array_merge($_GET, $match['params']);
        } elseif (!isset($match['friendly'])) {
            $handler = ($match != '/' && $match != '') ? $match : $this->_default_route;
        }

        return new $controller($handler);
    }

    /**
     * Compile the regex for a given route (EXPENSIVE).
     *
     * @param string $route
     *
     * @return string
     */
    protected function compile($route)
    {
        if (preg_match_all('`(/|\.|)\[([^:\]]*+)(?::([^:\]]*+))?\](\?|)`', $route, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                list($block, $pre, $type, $param, $optional) = $match;

                if (isset($this->_match_types[$type])) {
                    $type = $this->_match_types[$type];
                }
                if ($pre === '.') {
                    $pre = '\.';
                }

                //Older versions of PCRE require the 'P' in (?P<named>)
                $pattern = '(?:'
                    .($pre !== '' ? $pre : null)
                    .'('
                    .($param !== '' ? "?P<$param>" : null)
                    .$type
                    .'))'
                    .($optional !== '' ? '?' : null);

                $route = str_replace($block, $pattern, $route);
            }
        }

        return "`^$route$`u";
    }
}
