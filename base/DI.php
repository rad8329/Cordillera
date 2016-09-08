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

class DI
{
    /**
     * @var array
     */
    protected $_container = [];

    /**
     * @param string   $container
     * @param \Closure $definition
     */
    public function share($container, \Closure $definition)
    {
        $this->__set($container, function () use ($container, $definition) {

            if (!isset(Cordillera::$definitions[$container])) {
                Cordillera::$definitions[$container] = $definition();
            }

            return Cordillera::$definitions[$container];
        });
    }

    /**
     * @param $container
     */
    public function destroy($container)
    {
        unset(Cordillera::$instance->{$container});
        unset(Cordillera::$definitions[$container]);
        unset($this->_container[$container]);
    }

    /**
     * @param $container
     * @param callable|\Closure $definition
     */
    public function __set($container, \Closure $definition)
    {
        $this->_container[$container] = $definition;
    }

    /**
     * @param $container
     *
     * @return mixed|null
     */
    public function __get($container)
    {
        return isset($this->_container[$container]) ? $this->_container[$container]($this) : null;
    }
}
