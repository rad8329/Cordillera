<?php

namespace cordillera\helpers;

class Url
{
    /**
     * @param string $handler
     * @param array  $params
     *
     * @return string
     */
    public static function relative($handler, array $params = [])
    {
        return substr(app()->request->base_url, 0, -1).app()->router->generate($handler, $params);
    }

    /**
     * @param string $handler
     * @param array  $params
     *
     * @return string
     */
    public static function absolute($handler, array $params = [])
    {
        return substr(app()->request->base_url, 0, -1).app()->router->generate($handler, $params);
    }
}
