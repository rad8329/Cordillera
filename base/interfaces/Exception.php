<?php

namespace cordillera\base\interfaces;

interface Exception
{
    /**
     * @return array
     */
    public function getAllTraces();

    /**
     * @return string
     */
    public function toHtml();
}
