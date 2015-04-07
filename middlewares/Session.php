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

class Session extends \SessionHandler
{
    /**
     * @var string
     */
    protected $_key;

    /**
     * @var integer
     */
    protected $_lifetime;

    /**
     * @var string
     */
    protected $_path;

    /**
     * @var array
     */
    protected $_cookie;

    /**
     * @var string
     */
    protected $_name;


    /**
     * @param string $key
     * @param string $path
     * @param integer $lifetime
     * @param array $cookie
     */
    public function __construct($key, $path, $lifetime, array $cookie = [])
    {
        $this->_key = $key;
        $this->_path = $path;
        $this->_lifetime = $lifetime;
        $this->_cookie = $cookie;
        $this->init();
    }

    protected function init()
    {
        $this->_name = md5(
            $this->_key .
            $_SERVER['HTTP_USER_AGENT'] .
            (ip2long($_SERVER['REMOTE_ADDR']) & ip2long('255.255.0.0'))
        );

        $this->_cookie += [
            'lifetime' => 0,
            'path' => isset($this->_cookie['path']) ? $this->_cookie['path'] : '/',
            'domain' => isset($this->_cookie['domain']) ? $this->_cookie['domain'] : '',
            'secure' => isset($this->_cookie['secure']) ? $this->_cookie['secure'] : isset($_SERVER['HTTPS']),
            'httponly' => isset($this->_cookie['httponly']) ? $this->_cookie['httponly'] : true
        ];

        $this->setup();
    }

    protected function check()
    {
        if (!$this->isValid($this->_lifetime)) {
            $this->destroy(session_id());
        }
    }

    protected function setup()
    {
        ini_set('session.use_cookies', 1);
        ini_set('session.use_only_cookies', 1);

        session_name($this->_name);

        session_set_cookie_params(
            $this->_cookie['lifetime'],
            $this->_cookie['path'],
            $this->_cookie['domain'],
            $this->_cookie['secure'],
            $this->_cookie['httponly']
        );

        ini_set('session.save_handler', 'files');
        session_set_save_handler($this, true);
        session_save_path($this->_path);

        $this->start();
        $this->check();
    }

    /**
     * @return bool
     */
    protected function start()
    {
        if (session_id() === '') {
            if (session_start()) {

                return mt_rand(0, 4) === 0 ? $this->refresh() : true; // 1/5
            }
        }

        return false;
    }

    public function destroy($id)
    {
        $file = $this->_path . 'sess_' . $id;
        parent::destroy($id);
        if (file_exists($file)) {
            unlink($file);
        }

        return true;
    }

    public function forget()
    {
        if (session_id() === '') {
            return false;
        }

        $_SESSION = [];

        setcookie(
            $this->_name,
            '',
            time() - 42000,
            $this->_cookie['path'],
            $this->_cookie['domain'],
            $this->_cookie['secure'],
            $this->_cookie['httponly']
        );

        return session_destroy();
    }

    public function refresh()
    {
        return session_regenerate_id(true);
    }

    /**
     * @param string $id
     * @return string
     */
    public function read($id)
    {
        return mcrypt_decrypt(MCRYPT_3DES, $this->_key, parent::read($id), MCRYPT_MODE_ECB);
    }

    /**
     * @param string $id
     * @param string $data
     * @return bool
     */
    public function write($id, $data)
    {
        return parent::write(
            $id,
            mcrypt_encrypt(MCRYPT_3DES, $this->_key, $data, MCRYPT_MODE_ECB)
        );
    }

    /**
     * @param int $ttl
     * @return bool
     */
    public function isExpired($ttl = 30)
    {
        $last = isset($_SESSION['_last_activity']) ? $_SESSION['_last_activity'] : false;

        if ($last !== false && (time() - $last) > ($ttl * 60)) {
            return true;
        }

        $_SESSION['_last_activity'] = time();

        return false;
    }

    /**
     * @return bool
     */
    public function isFingerprint()
    {
        $hash = md5($_SERVER['HTTP_USER_AGENT'] . (ip2long($_SERVER['REMOTE_ADDR']) & ip2long('255.255.0.0')));

        if (isset($_SESSION['_fingerprint'])) {
            return $_SESSION['_fingerprint'] === $hash;
        }

        $_SESSION['_fingerprint'] = $hash;

        return true;
    }

    /**
     * @param int $ttl minutes
     * @return bool
     */
    protected function isValid($ttl = 30)
    {
        return !$this->isExpired($ttl) && $this->isFingerprint();
    }

    /**
     * @param string $name
     * @param null $default
     * @return mixed
     */
    public function get($name, $default = null)
    {
        $parsed = explode('.', $name);

        $result = $_SESSION;

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

    /**
     * @param string $name
     * @param mixed $value
     */
    public function put($name, $value)
    {
        $parsed = explode('.', $name);

        $session = &$_SESSION;

        while (count($parsed) > 1) {
            $next = array_shift($parsed);

            if (!isset($session[$next]) || !is_array($session[$next])) {
                $session[$next] = [];
            }

            $session =& $session[$next];
        }

        $session[array_shift($parsed)] = $value;
    }

    /**
     * @param string $name
     */
    public function clean($name)
    {
        $parsed = implode("", array_map(function ($name) {
                return ("['$name']");
            }, explode(".", $name))
        );

        if ($parsed) {
            eval('if(isset($_SESSION' . $parsed . ')) unset($_SESSION' . $parsed . ');');
        }
    }
}