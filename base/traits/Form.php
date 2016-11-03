<?php

namespace cordillera\base\traits;

use cordillera\helpers\Crypt;

trait Form
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
     *
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
     *
     * @return array
     */
    public function getErrors($attribute)
    {
        return isset($this->_errors[$attribute]) ? $this->_errors[$attribute] : [];
    }

    /**
     * @return array
     */
    public function getAllErrors()
    {
        return $this->_errors;
    }

    /**
     * @param string $name
     *
     * @return string
     */
    public function field($name)
    {
        if (property_exists($this, $name)) {
            return Crypt::request((new \ReflectionClass($this))->getShortName()).'['.Crypt::request($name).']';
        }

        return Crypt::request($name);
    }
}
