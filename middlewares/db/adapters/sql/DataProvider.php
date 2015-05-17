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

namespace cordillera\middlewares\db\adapters\sql;

use cordillera\middlewares\Exception;

class DataProvider
{
    /**
     * @var string
     */
    protected $_query_conditions = '';

    /**
     * @var array
     */
    protected $_query_params = [];

    /**
     * @var ActiveRecord|Query
     */
    protected $_data_source;

    /**
     * @var Sorter[]
     */
    protected $_sorters = [];

    /**
     * @var array
     */
    public $request_params = [];

    /**
     * @var string
     */
    public $request_context = '';

    /**
     * @var int
     */
    public $records_per_page = 25;

    /**
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        $this->init($config);
    }

    protected function init(array $config = [])
    {
        unset($config['request_params'], $config['request_context']);

        if (!isset($config['data_source'])) {
            throw new Exception(translate('{data_source} must be a Query|ActiveRecord objetc'), 500, Exception::BADARGUMENTS);
        } elseif (
            isset($config['data_source']) &&
            (($config['data_source'] instanceof self) && ($config['data_source'] instanceof Query))
        ) {
            throw new Exception(translate('{data_source} must be a Query|ActiveRecord objetc'), 500, Exception::BADARGUMENTS);
        }

        foreach ($config as $property => $data) {
            if (property_exists($this, '_'.$property)) {
                $this->{'_'.$property} = $data;
            }
        }

        $this->extractRequest();
        //@TODO: Filters logic
        //$this->setupFilters();
    }

    private function extractRequest()
    {
        //$this->_request_context = (new \ReflectionClass($this->_data_source))->getShortName();
        //$this->_request_params = app()->request->get($this->_request_context);
    }

    public function getData()
    {
    }

    public function getTotalRecords()
    {
    }

    public function export()
    {
    }
}
