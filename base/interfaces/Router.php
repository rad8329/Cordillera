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

use cordillera\middlewares\Controller;

interface Router
{
    /**
     * @param array $routes
     */
    public function add(array $routes);

    /**
     * Map a route to a target
     *
     * @param string $route The route regex, custom regex must start with an @.
     * You can use multiple pre-set regex filters, like [i:id]
     * @param mixed $handler The handler target where this route should point to.
     */
    public function map($route, $handler);

    /**
     * Reversed routing
     *
     * Generate the URL for a named route. Replace regexes with supplied parameters
     *
     * @param string $route The name of the route.
     * @param array @params Associative array of parameters to replace placeholders with.
     * @return string The URL of the route with named parameters in place.
     */
    public function generate($route, array $params = []);

    /**
     * @return Controller
     */
    public function dispatch();

    /**
     * Match a given Request Url against stored routes
     * @param string $request_url
     * @return array|boolean Array with route information on success, false on failure (no match).
     */
    public function match($request_url = null);
}