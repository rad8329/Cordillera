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

class Config
{
    /**
     * @var array
     */
    protected $_config = [];

    /**
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->_config = $config;
    }

    /**
     * Example: Application::getConfig()->get("response.default");.
     *
     * @param string $config  A string separate by point
     * @param null   $default
     *
     * @return mixed
     */
    public function get($config, $default = null)
    {
        $parsed = explode('.', $config);

        $result = $this->_config;

        while ($parsed) {
            $next = array_shift($parsed);

            if (isset($result[$next])) {
                $result = $result[$next];
            } else {
                return $default;
            }
        }

        return $result;
    }
}
