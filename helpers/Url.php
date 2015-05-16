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

namespace cordillera\helpers;

use cordillera\base\Cordillera;

class Url
{
    /**
     * @param string $handler
     * @param array  $params
     *
     * @return string
     */
    public static function relative($handler, $params = [])
    {
        return substr(Cordillera::app()->request->base_url, 0, -1).Cordillera::app()->router->generate($handler, $params);
    }

    /**
     * @param string $handler
     * @param array  $params
     *
     * @return string
     */
    public static function absolute($handler, $params = [])
    {
        return substr(Cordillera::app()->request->base_url, 0, -1).Cordillera::app()->router->generate($handler, $params);
    }
}
