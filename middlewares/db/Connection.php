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
     * @var array
     */
    protected $_options = [
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ];

    /**
     * @param string $dsn
     * @param string $username
     * @param string $password
     * @param array $options
     * @throws Exception
     */
    public function __construct($dsn, $username, $password, array $options = [])
    {
        $this->_dsn = $dsn;
        $this->_username = $username;
        $this->_password = $password;
        $this->_options = array_merge($this->_options, $options);

        $this->init();
    }

    protected function init()
    {
        try {
            parent::__construct($this->_dsn, $this->_username, $this->_password, $this->_options);
        } catch (\PDOException $e) {
            throw new Exception($e->getMessage(), 500, Exception::DBCONNECTION);
        }
    }

    /**
     * @param string $statement
     * @param array $driver_options
     * @return \PDOStatement
     * @throws Exception
     */
    public function prepare($statement, $driver_options = [])
    {
        try {
            return parent::prepare($statement, $driver_options);
        } catch (\PDOException $e) {
            throw new Exception($e->getMessage(), 500, Exception::DBSTATEMENT);
        }
    }

    /**
     * @param array $input_parameters
     * @return mixed
     * @throws Exception
     */
    public function execute(array $input_parameters = null)
    {
        try {
            return $this->execute($input_parameters);
        } catch (\PDOException $e) {
            throw new Exception($e->getMessage(), 500, Exception::SQL);
        }
    }
}