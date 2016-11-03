<?php

namespace cordillera\middlewares\db\adapters\sql;

class Expression
{
    /**
     * @var string
     */
    protected $_expression = null;

    /**
     * @param string $expression
     */
    public function __construct($expression)
    {
        $this->_expression = $expression;
    }

    /**
     * @return string
     */
    public function toSql()
    {
        return $this->_expression;
    }
}
