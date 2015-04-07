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

class Expression
{
    /**
     * @var string
     */
    protected $_expression = null;

    /**
     * @param string $expression
     */
    public function __construct($expression)
    {
        $this->_expression = $expression;
    }

    /**
     * @return string
     */
    public function toSql()
    {
        return $this->_expression;
    }
}