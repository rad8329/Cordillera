<?php

namespace cordillera\helpers;

class Crypt
{
    /**
     * @param string $text
     *
     * @return string
     */
    public static function hash($text)
    {
        return md5($text.app()->request->salt);
    }

    /**
     * @param int $lenght
     *
     * @return string
     */
    public static function create($lenght)
    {
        return bin2hex(mcrypt_create_iv($lenght, MCRYPT_RAND));
    }

    /**
     * @param string $name
     *
     * @return string
     */
    public static function request($name)
    {
        if (app()->config->get('request.csrf')) {
            $name = self::hash($name);
        }

        return $name;
    }

    /**
     * @param $key
     *
     * @return bool|string
     */
    public static function pad($key)
    {
        // key is too large
        if (strlen($key) > 32) {
            return false;
        }

        // set sizes
        $sizes = array(16, 24, 32);

        // loop through sizes and pad key
        foreach ($sizes as $s) {
            while (strlen($key) < $s) {
                $key = $key."\0";
            }
            if (strlen($key) == $s) {
                break;
            } // finish if the key matches a size
        }

        return $key;
    }
}
