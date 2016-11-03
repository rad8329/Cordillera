<?php

namespace cordillera\base\interfaces;

interface DataProvider
{
    public function extractRequest();

    /**
     * @return mixed
     */
    public function getData();

    /**
     * @return int
     */
    public function getTotalRecords();

    /**
     * @return int
     */
    public function getTotalRecordsPage();

    public function export();

    /**
     * @return bool
     */
    public function isActiveRecord();

    public function applyFilters();
}
