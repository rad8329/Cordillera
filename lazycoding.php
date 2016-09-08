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
 * @return \cordillera\base\Application
 */
function app()
{
    return \cordillera\base\Cordillera::app();
}

/**
 * @param string $text
 * @param array  $params
 * @param string $source
 *
 * @return string
 */
function translate($text, $params = [], $source = 'app')
{
    return app()->lang->translate($text, $params, $source);
}

/**
 * @return \cordillera\middlewares\Logger
 */
function logger()
{
    return app()->logger;
}

/**
 * @param mixed $args
 */
function dump($args)
{
    $print = print_r($args, 1);

    if (isset(app()->request) && app()->request->isAjax()) {
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
