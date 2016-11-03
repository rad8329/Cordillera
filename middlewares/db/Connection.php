<?php

namespace cordillera\middlewares\db;

use cordillera\middlewares\Exception;
use PDO;

class Connection extends PDO
{
    /**
     * @var string
     */
    protected $_dsn;

    /**
     * @var string
     */
    protected $_username;

    /**
     * @var string
     */
    protected $_password;

    /**
     * @var string
     */
    public $_last_statement = '';

    /**
     * @var array
     */
    protected $_options = [
        PDO::ATTR_EMULATE_PREPARES => true,
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ];

    /**
     * @param string $dsn
     * @param string $username
     * @param string $password
     * @param array  $options
     */
    public function __construct($dsn, $username, $password, array $options = [])
    {
        $this->_dsn = $dsn;
        $this->_username = $username;
        $this->_password = $password;
        $this->_options = array_merge($this->_options, $options);

        $this->init($dsn, $username, $password, $options);
    }

    /**
     * @param string $dsn
     * @param string $username
     * @param string $password
     * @param array  $options
     *
     * @throws Exception
     */
    protected function init($dsn, $username, $password, array $options = [])
    {
        try {
            //With parameters of method scope, because if there is a exception, this object is destroyed
            parent::__construct($dsn, $username, $password, $options);
            $this->setAttribute(PDO::ATTR_STATEMENT_CLASS, ['\cordillera\middlewares\db\Statement', [$this]]);
        } catch (\PDOException $e) {
            logger()->critical('DB connection failed', [
                    'dsn' => $dsn,
                    'username' => $username,
                    'password' => $password,
                    'options' => $options,
                ]);
            throw new Exception($e->getMessage(), 500, Exception::DBCONNECTION);
        }
    }

    /**
     * @param string $statement
     * @param array  $driver_options
     *
     * @return Statement|\PDOStatement
     *
     * @throws Exception
     */
    public function prepare($statement, $driver_options = [])
    {
        try {
            $this->_last_statement = $statement;

            return parent::prepare($statement, $driver_options);
        } catch (\PDOException $e) {
            throw new Exception($e->getMessage(), 500, Exception::DBSTATEMENT);
        }
    }
}
