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

interface Lang
{
    /**
     * @param string $text
     * @param array  $params
     * @param string $source
     *
     * @return string
     */
    public function translate($text, array $params = [], $source = '');

    /**
     * @param string $source
     *
     * @return array
     **/
    public function load($source);
}
