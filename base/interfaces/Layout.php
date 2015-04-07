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

interface Layout
{
    /**
     * @param $property
     * @param $default
     * @return mixed
     */
    public function getProperty($property, $default = "");

    /**
     * @param string $file A valid js file name
     */
    public function registerJsFile($file);

    /**
     * @param string $file A valid css file name
     */
    public function registerCssFile($file);

    /**
     * @return string Content (HTML) of js and css tags
     */
    public function publishRegisteredFiles();
}