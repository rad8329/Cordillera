<?php

namespace cordillera\middlewares\db\adapters\sql;

class Sorter
{
    /**
     * @var string
     */
    public $request_param = '';

    /**
     * @var string
     */
    public $attribute = '';

    /**
     * @var string
     */
    public $order = '';

    /**
     * @param string        $request_param
     * @param string        $attribute
     * @param string string $order
     */
    public function __construct($request_param, $attribute, $order = 'ASC')
    {
        $this->request_param = $request_param;
        $this->attribute = $attribute;
        $this->order = $order;
    }
}
