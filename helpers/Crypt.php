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

use cordillera\base\Application;

class Crypt
{
    /**
     * @param string $text
     *
     * @return string
     */
    public static function hash($text)
    {
        return md5($text.Application::getRequest()->salt);
    }

    /**
     * @param int $lenght
     *
     * @return string
     */
    public static function create($lenght)
    {
        return bin2hex(mcrypt_create_iv($lenght, MCRYPT_RAND));
    }

    /**
     * @param string $name
     *
     * @return string
     */
    public static function requestVar($name)
    {
        if (Application::getConfig()->get('request.csrf')) {
            $name = self::hash($name);
        }

        return $name;
    }
}
