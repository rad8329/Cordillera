<?php

namespace cordillera\base\interfaces;

use cordillera\middlewares\View;

interface Controller
{
    /**
     * @param callable|\Closure $definition
     */
    public function put(\Closure $definition);

    /**
     * @param callable|\Closure $definition
     */
    public function get(\Closure $definition);

    /**
     * @param callable|\Closure $definition
     */
    public function post(\Closure $definition);

    /**
     * @param callable|\Closure $definition
     */
    public function delete(\Closure $definition);

    /**
     * @param callable|\Closure $definition
     */
    public function head(\Closure $definition);

    /**
     * @param callable|\Closure $definition
     */
    public function trace(\Closure $definition);

    /**
     * @param callable|\Closure $definition
     */
    public function options(\Closure $definition);

    /**
     * @param callable|\Closure $definition
     */
    public function filters(\Closure $definition);

    /**
     * @param View|array|string $response
     */
    public function setResponse($response);
}
