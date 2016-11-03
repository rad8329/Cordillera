<?php

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
