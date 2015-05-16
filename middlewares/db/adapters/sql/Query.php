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

class Query
{
    /**
     * @var array
     */
    protected $_config;

    /**
     * @var string
     */
    protected $_sql = '';

    /**
     * @var array
     */
    public $condition = [];
    /**
     * @var array
     */
    public $join = [];
    /**
     * @var array
     */
    public $group = [];

    /**
     * @var string
     */
    public $select = '*';

    /**
     * @var array
     */
    public $from = [];

    /**
     * @var string
     */
    public $limit = '';

    /**
     * @var string
     */
    public $offset = '';

    /**
     * @var array
     */
    public $order = [];

    /**
     * @var array
     */
    public $params = [];

    /**
     * Example: Route::findAll([
     *      'condition' => 'T.zone_id = :zone_id',
     *      'select' => 'T.id,T.`code`,T.`name`,T.zone_id',
     *      'params' => [':zone_id' => $zone->id]
     *    ]);.
     *
     * @param array $config
     */
    public function __construct($config = [])
    {
        $this->_config = $config;
        $this->init();
    }

    protected function init()
    {
        if (isset($this->_config['condition'])) {
            if (is_array($this->_config['condition'])) {
                foreach ($this->_config['condition'] as $condition) {
                    $this->addCondition($condition);
                }
            } else {
                $this->addCondition($this->_config['condition']);
            }
        }
        if (isset($this->_config['join'])) {
            if (is_array($this->_config['join'])) {
                foreach ($this->_config['join'] as $join) {
                    $this->addJoin($join);
                }
            } else {
                $this->addJoin($this->_config['join']);
            }
        }
        if (isset($this->_config['group'])) {
            if (is_array($this->_config['group'])) {
                foreach ($this->_config['group'] as $group) {
                    $this->addGroup($group);
                }
            } else {
                $this->addGroup($this->_config['group']);
            }
        }
        if (isset($this->_config['from'])) {
            if (is_array($this->_config['from'])) {
                foreach ($this->_config['from'] as $from) {
                    $this->addFrom($from);
                }
            } else {
                $this->addFrom($this->_config['from']);
            }
        }
        if (isset($this->_config['order'])) {
            if (is_array($this->_config['order'])) {
                foreach ($this->_config['order'] as $order) {
                    $this->addOrder($order);
                }
            } else {
                $this->addOrder($this->_config['order']);
            }
        }
        if (isset($this->_config['select'])) {
            $this->select = $this->_config['select'];
        }
        if (isset($this->_config['limit'])) {
            $this->limit = (int) $this->_config['limit'];
        }
        if (isset($this->_config['offset'])) {
            $this->offset = (int) $this->_config['offset'];
        }
        if (isset($this->_config['params']) && is_array($this->_config['params'])) {
            $this->params = $this->_config['params'];
        }
    }

    /**
     * @param string $condition
     * @param string $operator
     */
    public function addCondition($condition, $operator = 'AND')
    {
        if (empty($this->condition)) {
            $operator = '';
        }
        $this->condition[] = " {$operator} {$condition}";
    }

    /**
     * @param string $join
     */
    public function addJoin($join)
    {
        $this->join[] = $join;
    }

    /**
     * @param string $group
     */
    public function addGroup($group)
    {
        $this->group[] = $group;
    }

    /**
     * @param string $from
     */
    public function addFrom($from)
    {
        $this->from[] = $from;
    }

    /**
     * @param string $order
     */
    public function addOrder($order)
    {
        $this->order[] = $order;
    }

    /**
     * @return string
     */
    public function toSql()
    {
        $this->_sql = "SELECT {$this->select} FROM ".implode(',', $this->from);
        if (!empty($this->join)) {
            $this->_sql .= ' '.implode(' ', $this->join);
        }
        if (!empty($this->condition)) {
            $this->_sql .= ' WHERE '.implode(' ', $this->condition);
        }
        if (!empty($this->group)) {
            $this->_sql .= ' GROUP BY '.implode(',', $this->group);
        }
        if (!empty($this->order)) {
            $this->_sql .= ' ORDER BY '.implode(',', $this->order);
        }
        if (!empty($this->limit)) {
            $this->_sql .= ' LIMIT '.$this->limit;
        }
        if (!empty($this->offset)) {
            $this->_sql .= ' OFFSET '.$this->offset;
        }

        return $this->_sql;
    }
}
