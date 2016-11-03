<?php

namespace cordillera\middlewares\db;

use cordillera\middlewares\Exception;

class Statement extends \PDOStatement
{
    /**
     * @var Connection
     */
    protected $pdo;

    /**
     * @param Connection $pdo
     */
    protected function __construct(Connection $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * @param array $input_parameters
     *
     * @return mixed
     *
     * @throws Exception
     */
    public function execute($input_parameters = [])
    {
        try {
            $return = parent::execute($input_parameters);

            if (!$return) {
                $error = $this->errorInfo();
                throw new \PDOException($error[2]);
            }

            app()->logger->debug('Execute statement', [
                'statement' => $this->pdo->_last_statement,
                'input_parameters' => $input_parameters,
            ]);

            return $return;
        } catch (\PDOException $e) {
            app()->logger->error('Execute statement failed', [
                    'statement' => $this->pdo->_last_statement,
                    'input_parameters' => $input_parameters,
                    'error' => $e->getMessage(),
                ]);
            throw new Exception($e->getMessage(), 500, Exception::QUERY);
        }
    }
}
