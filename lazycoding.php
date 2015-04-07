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

/**
 * @param mixed $args
 */
function dump($args)
{
    $print = print_r($args, 1);

    if (\cordillera\middlewares\Request::isAjax()) {
        echo $print;
    } else {
        $print = str_replace(['<', '>'], ['&lt;', '&gt;'], $print);
        echo "<pre>$print</pre>";
    }
}

/**
 * @param mixed $args
 */
function dumpx($args)
{
    dump($args);
    exit;
}

/**
 * @param string $text
 * @param array $params
 * @param string $source
 * @return string
 */
function translate($text, $params = [], $source = 'app')
{
    return \cordillera\base\Application::getLang()->translate($text, $params, $source);
}
