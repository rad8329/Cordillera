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

use cordillera\middlewares\View;

interface Controller
{
    /**
     * @param callable $definition
     */
    public function put(\Closure $definition);

    /**
     * @param callable $definition
     */
    public function get(\Closure $definition);

    /**
     * @param callable $definition
     */
    public function post(\Closure $definition);

    /**
     * @param callable $definition
     */
    public function delete(\Closure $definition);

    /**
     * @param callable $definition
     */
    public function filters(\Closure $definition);

    /**
     * @param View|array|string $response
     */
    public function setResponse($response);
}
