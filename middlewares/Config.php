<?php

namespace cordillera\middlewares;

class Config
{
    /**
     * @var array
     */
    protected $_config = [];

    /**
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->_config = $config;
    }

    /**
     * Example: app()->config->get("response.default");.
     *
     * @param string $config  A string separate by point
     * @param null   $default
     *
     * @return mixed
     */
    public function get($config, $default = null)
    {
        $parsed = explode('.', $config);

        $result = $this->_config;

        while ($parsed) {
            $next = array_shift($parsed);

            if (isset($result[$next])) {
                $result = $result[$next];
            } else {
                return $default;
            }
        }

        return $result;
    }
}
