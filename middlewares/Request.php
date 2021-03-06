<?php

namespace cordillera\middlewares;

use cordillera\helpers\Crypt;

class Request
{
    use \cordillera\base\traits\Request;

    /**
     * @var bool
     */
    protected $_csrf;

    /**
     * @var Session
     */
    protected $_session;

    /**
     * @var bool
     */
    public $ssl = false;

    /**
     * @var string
     */
    public $base_url = '';

    /**
     * @var string
     */
    public $script_name = '';

    /**
     * @var string
     */
    public $home = '';

    /**
     * @var string
     */
    public $server_name = '';

    /**
     * @var string
     */
    public $absolute_url = '';

    /**
     * @var string
     */
    public $port = '';

    /**
     * @var string
     */
    public $csrf_id = '';

    /**
     * @var string
     */
    public $csrf_value = '';

    /**
     * @var string
     */
    public $salt = '';

    /**
     * @var array
     */
    public static $payload = [];

    /**
     * @var array
     */
    public $headers = [];

    /**
     * @param Session $session
     * @param bool    $csrf
     */
    public function __construct(Session $session, $csrf = true)
    {
        $this->_session = $session;
        $this->_csrf = $csrf;
        $this->init();
    }

    protected function init()
    {
        $this->headers = getallheaders();
        $this->script_name = basename($_SERVER['SCRIPT_NAME']);
        $this->ssl = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? true : false;
        $this->server_name = $_SERVER['SERVER_NAME'];

        $tmp_base_url = array_filter(explode('/'.$this->script_name, $_SERVER['PHP_SELF']));

        $this->base_url = count($tmp_base_url) > 1 ? $tmp_base_url[0] : str_replace('\\', '/', str_replace($this->script_name, '', pathinfo($_SERVER['PHP_SELF'])['dirname']));

        $this->base_url = strlen($this->base_url) > 1 ? $this->base_url.'/' : $this->base_url;

        $this->port = $_SERVER['SERVER_PORT'];
        $this->home = $this->base_url.($this->script_name != 'index.php' ? '/'.$this->script_name : '');

        $this->absolute_url = ($this->ssl ? 'https' : 'http').'://';
        $this->absolute_url .= $this->server_name.(($this->port == 80) ? '' : ':'.$this->port).$this->base_url;

        $this->csrf_id = $this->_session->get('request.csrf_id');
        $this->csrf_value = $this->_session->get('request.csrf_value');
        $this->salt = $this->_session->get('request.salt');

        if ($this->_csrf) {
            if (!$this->csrf_id) {
                $this->csrf_id = Crypt::create(10);
                $this->_session->put('request.csrf_id', $this->csrf_id);
            }
            if (!$this->csrf_value) {
                $this->csrf_value = hash('sha256', Crypt::create(500));
                $this->_session->put('request.csrf_value', $this->csrf_value);
            }
            if (!$this->salt) {
                $this->salt = Crypt::create(32);
                $this->_session->put('request.salt', $this->salt);
            }
        }
    }

    /**
     * @param string $name
     * @param mixed  $default
     *
     * @return mixed
     */
    public function payload($name, $default = null)
    {
        if (!static::$payload) {
            static::$payload = json_decode(file_get_contents('php://input'), true);
        }

        $parsed = explode('.', $name);

        $result = static::$payload;

        while ($parsed) {
            $next = array_shift($parsed);
            $next_crypted = Crypt::request($next);

            if (isset($result[$next])) {
                $result = $result[$next];
            } elseif ($next_crypted != $next && isset($result[$next_crypted])) {
                $result = $result[$next_crypted];
            } else {
                return $default;
            }
        }

        return $result;
    }

    /**
     * @param string $name
     * @param mixed  $default
     *
     * @return mixed
     */
    public function get($name, $default = null)
    {
        return isset($_GET[$name]) ? $_GET[$name] : $default;
    }

    /**
     * @param string $name
     * @param mixed  $default
     *
     * @return mixed
     */
    public function post($name, $default = null)
    {
        $parsed = explode('.', $name);

        $result = $_POST;

        while ($parsed) {
            $next = array_shift($parsed);
            $next_crypted = Crypt::request($next);

            if (isset($result[$next])) {
                $result = $result[$next];
            } elseif ($next_crypted != $next && isset($result[$next_crypted])) {
                $result = $result[$next_crypted];
            } else {
                return $default;
            }
        }

        return $result;
    }
}
