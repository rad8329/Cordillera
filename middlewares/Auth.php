<?php

namespace cordillera\middlewares;

use cordillera\base\interfaces\Auth as AuthInterface;

class Auth implements AuthInterface
{
    /**
     * @var Session
     */
    protected $_session;

    /**
     * @var mixed
     */
    public $id = null;

    /**
     * @var array
     */
    public $data = [];

    /**
     * @param Session $session
     */
    public function __construct(Session $session)
    {
        $this->_session = $session;
        $this->init();
    }

    protected function init()
    {
        if ($this->_session->get('auth.id')) {
            $this->id = $this->_session->get('auth.id');
            $this->data = $this->_session->get('auth.data');
        }
    }

    /**
     * @param mixed $id
     * @param array $data
     */
    public function login($id, array $data = [])
    {
        $this->_session->put('auth.id', $id);
        $this->_session->put('auth.data', $data);
        $this->_session->refresh();
    }

    public function logout()
    {
        $this->_session->refresh();
        session_destroy();
    }
}
