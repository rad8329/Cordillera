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

namespace cordillera\middlewares;

use cordillera\base\interfaces\Lang as LangInterface;

class Lang implements LangInterface
{
    /**
     * @var string
     */
    public $language;

    /**
     * @var array The languages files are cached into this variable
     */
    protected $_messages = [];

    /**
     * @param string $language Default application language
     */
    public function __construct($language)
    {
        $this->language = $language;
    }

    /**
     * @param string $text
     * @param array $params
     * @param string $source
     * @return string
     */
    public function translate($text, array $params = [], $source = 'app')
    {
        $messages = $this->load($source);
        $message = vsprintf(isset($messages[$text]) ? $messages[$text] : $text, $params);

        return $message ? $message : $text;
    }

    /**
     * @param string $source
     * @return array
     **/
    public function load($source)
    {
        if (isset($this->_messages[$source])) {
            return $this->_messages[$source];
        } else {
            $filename_cordillera = CORDILLERA_DIR . 'languages' . DS . $this->language . DS . $source . '.php';
            $filename_app = CORDILLERA_APP_DIR . 'languages' . DS . $this->language . DS . $source . '.php';

            $this->_messages[$source] = is_file($filename_app) ?
                require_once $filename_app : (is_file($filename_cordillera) ? require_once $filename_cordillera : []);
        }

        return $this->_messages[$source];
    }
}