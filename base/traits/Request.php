<?php

namespace cordillera\base\traits;

trait Request
{
    /**
     * @return string
     */
    public function getMethod()
    {
        return $_SERVER['REQUEST_METHOD'];
    }

    /**
     * @return bool
     */
    public function isPost()
    {
        if ($this->getMethod() == 'POST') {
            return true;
        }

        return false;
    }

    /**
     * @return bool
     */
    public function isOptions()
    {
        if ($this->getMethod() == 'OPTIONS') {
            return true;
        }

        return false;
    }

    /**
     * @return bool
     */
    public function isAjax()
    {
        if (!(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest'))) {
            return false;
        }

        return true;
    }

    /**
     * Convert any string (including php headers with HTTP prefix) to header format like :
     *  * X-Pingother -> HTTP_X_PINGOTHER
     *  * X PINGOTHER -> HTTP_X_PINGOTHER.
     *
     * @param string $string string to convert
     *
     * @return string the result in "php $_SERVER header" format
     */
    public function headerizeToPhp($string)
    {
        return 'HTTP_'.strtoupper(str_replace([' ', '-'], ['_', '_'], $string));
    }

    /**
     * Convert any string (including php headers with HTTP prefix) to header format like :
     *  * X-PINGOTHER -> X-Pingother
     *  * X_PINGOTHER -> X-Pingother.
     *
     * @param string $string string to convert
     *
     * @return string the result in "header" format
     */
    public function headerize($string)
    {
        $headers = preg_split('/[\\s,]+/', $string, -1, PREG_SPLIT_NO_EMPTY);
        $headers = array_map(function ($element) {
            return str_replace(' ', '-', ucwords(strtolower(str_replace(['_', '-'], [' ', ' '], $element))));
        }, $headers);

        return implode(', ', $headers);
    }
}
