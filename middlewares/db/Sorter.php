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

namespace cordillera\middlewares\db;

class Sorter
{
    /**
     * @var string
     */
    public $request_param = '';

    /**
     * @var string
     */
    public $attribute = '';

    /**
     * @var string
     */
    public $order = '';

    /**
     * @param $rquest_param
     * @param $attribute
     * @param string $order
     */
    public function __construct($request_param, $attribute, $order = 'ASC')
    {
        $this->request_param = $request_param;
        $this->attribute = $attribute;
        $this->order = $order;
    }
}
