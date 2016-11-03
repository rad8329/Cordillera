<?php

namespace cordillera\middlewares\db\adapters\sql\providers;

use cordillera\middlewares\db\ActiveDataProvider;
use cordillera\middlewares\db\adapters\sql\Query;
use cordillera\middlewares\db\adapters\sql\Sorter;

class ActiveRecord extends ActiveDataProvider
{
    /**
     * @var Query
     */
    public $query;

    /**
     * @var Sorter[]
     */
    public $sorters = [];

    /**
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        $this->init($config);
    }

    protected function init(array $config = [])
    {
        $this->setup($config);

        $this->query = new Query(
            ['limit' => $this->records_per_page]
        );

        $this->extractRequest();
        $this->applyFilters();
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data_source->findAll($this->query);
    }

    /**
     * @return int
     */
    public function getTotalRecords()
    {
        $query = clone $this->query;
        $query->limit = '';

        return $this->data_source->count($query);
    }

    /**
     * @return int
     */
    public function getTotalRecordsPage()
    {
        return $this->data_source->count($this->query);
    }

    public function export()
    {
    }
}
