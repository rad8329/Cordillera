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

use cordillera\helpers\Crypt;

trait Model
{
    /**
     * @var array
     */
    protected $_errors = [];

    /**
     * @return bool
     */
    public function validate()
    {
        return true;
    }

    /**
     * @return bool
     */
    public function hasErrors()
    {
        return !empty($this->_errors);
    }

    /**
     * @param string $property
     * @return bool
     */
    public function hasError($property)
    {
        return isset($this->_errors[$property]);
    }

    /**
     * @param string $attribute
     * @param string $message
     */
    public function addError($attribute, $message)
    {
        $this->_errors[$attribute][] = $message;
    }

    /**
     * @param string $attribute
     * @return array
     */
    public function getErrors($attribute)
    {
        return isset($this->_errors[$attribute]) ? $this->_errors[$attribute] : [];
    }

    /**
     * @param string $name
     * @return string
     */
    public function fieldName($name)
    {
        return Crypt::requestVar($name);
    }
}