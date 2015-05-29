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

namespace cordillera\base\interfaces;

use cordillera\middlewares\db\adapters\sql\Query;
use cordillera\middlewares\db\Connection;

interface ActiveRecord
{
    /**
     * @return Connection
     */
    public function getDB();

    /**
     * @return bool
     */
    public function isDirty();

    /**
     * @param bool $dirty
     */
    public function setDirty($dirty = true);

    /**
     * @return bool
     */
    public function isNew();

    /**
     * @param mixed $pk_value
     *
     * @return \stdClass
     */
    public static function findByPk($pk_value);

    /**
     * @param Query|array|bool $args
     *
     * @return mixed
     */
    public static function find($args = false);

    /**
     * @param Query|array|bool $args
     *
     * @return array
     */
    public static function findAll($args = false);

    /**
     * @param Query|array|bool $args
     *
     * @return int
     */
    public static function count($args = false);

    /**
     * @param array $data
     */
    public function bind($data = []);

    /**
     * @return bool
     */
    public function delete();

    /**
     * @param bool $validate
     */
    public function save($validate = true);
}
