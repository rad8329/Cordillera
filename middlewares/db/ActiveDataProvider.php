<?php

namespace cordillera\middlewares\db;

use cordillera\base\interfaces\DataProvider as DataProviderInterface;
use cordillera\middlewares\Exception;
use cordillera\base\interfaces\ActiveRecord as ActiveRecordInterface;

abstract class ActiveDataProvider implements DataProviderInterface
{
    /**
     * @var \Closure
     */
    public $search;

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
     * @var ActiveRecordInterface
     */
    public $data_source;

    /**
     * @param array $config
     *
     * @throws Exception
     */
    public function setup(array $config = [])
    {
        unset($config['request_params'], $config['request_context']);

        if (isset($config['query'])) {
            throw new Exception(translate('{query} can not be initialized'), 500, Exception::BADARGUMENTS);
        }

        if (isset($config['search']) && !is_callable($config['search'])) {
            throw new Exception(translate('{search} must be a callable'), 500, Exception::BADARGUMENTS);
        }

        if (!isset($config['data_source'])) {
            throw new Exception(translate('{data_source} must be a ActiveRecord objetc'), 500, Exception::BADARGUMENTS);
        } elseif (isset($config['data_source']) && !($config['data_source'] instanceof ActiveRecordInterface)) {
            throw new Exception(translate('{data_source} must be a ActiveRecord objetc'), 500, Exception::BADARGUMENTS);
        }

        foreach ($config as $property => $data) {
            if (property_exists($this, '_'.$property)) {
                $this->{'_'.$property} = $data;
            } elseif (property_exists($this, $property)) {
                $this->{$property} = $data;
            }
        }
    }

    public function extractRequest()
    {
        $this->request_context = (new \ReflectionClass($this->data_source))->getShortName();
        $this->request_params = app()->request->get($this->request_context);
    }

    public function applyFilters()
    {
        if (is_callable($this->search)) {
            call_user_func_array($this->search, [$this]);
        }
    }

    /**
     * @return bool
     */
    public function isActiveRecord()
    {
        return true;
    }
}
