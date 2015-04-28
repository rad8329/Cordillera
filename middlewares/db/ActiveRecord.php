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

namespace cordillera\middlewares\db;

use cordillera\base\traits\Form;
use cordillera\middlewares\Exception;
use cordillera\base\Application;

abstract class ActiveRecord
{
    use Form;

    /**
     * @var string Table name
     */
    protected $_table_name = null;

    /**
     * @var mixed The primary key's table
     */
    protected $_pk_name = null;

    /**
     * @var bool
     */
    protected $_is_dirty = false;

    /**
     * @var bool true if is a new record
     */
    protected $_is_new = true;

    public function __construct()
    {
        $args = func_get_args();
        $this->init($args);
    }

    /**
     * @param array $args
     */
    protected function init($args)
    {
        if (is_array($args) && !empty($args)) {
            if (isset($args[0])) {
                $this->_is_new = $args[0];
            }
        }

        $this->afterFind();
    }

    /**
     * @return Connection
     */
    public function getDb()
    {
        return Application::getDb();
    }

    /**
     * @return bool
     */
    public function isDirty()
    {
        return $this->_is_dirty;
    }

    /**
     * @param bool $dirty
     */
    public function setDirty($dirty = true)
    {
        $this->_is_dirty = $dirty;
    }

    /**
     * @return string
     */
    public function getTableName()
    {
        return $this->_table_name;
    }

    /**
     * @return mixed
     */
    public function getPkName()
    {
        return $this->_pk_name;
    }

    /**
     * @return bool
     */
    public function isNew()
    {
        return $this->_is_new;
    }

    /**
     * @param mixed $pk_value
     *
     * @return \stdClass
     */
    public static function findByPk($pk_value)
    {
        /* @var ActiveRecord $model */

        $model = new static();
        $pk_name = $model->getPkName();

        if (is_array($pk_name)) {
            $pk_condition = [];
            foreach ($pk_name as $pk) {
                $pk_condition[] = "T.{$pk} = :{$pk}";
            }
            $pk_condition = implode(' AND ', $pk_condition);
        } else {
            $pk_condition = "T.{$pk_name} = :{$pk_name}";
        }

        $params = [];
        if (is_array($pk_name)) {
            foreach ($pk_name as $pk) {
                $params[":{$pk}"] = $pk_value[$pk];
            }
        } else {
            $params[":{$pk_name}"] = $pk_value;
        }

        return self::find(['condition' => $pk_condition, 'params' => $params]);
    }

    /**
     * @param Query|array|bool $args
     *
     * @return array
     *
     * @throws Exception
     */
    public static function find($args = false)
    {
        /* @var ActiveRecord $model */

        $query = null;
        $model = new static();

        if ($args && $args instanceof Query) {
            $query = $args;
            $query->limit = 1;

            if (empty($query->from)) {
                $query->addFrom($model->getTableName().' T');
            }
        } elseif ($args && is_array($args)) {
            $args['limit'] = 1;
            if (!isset($args['from'])) {
                $args['from'] = $model->getTableName().' T';
            }
            $query = new Query($args);
        } elseif (!$args) {
            $query = new Query(
                [
                    'from' => $model->getTableName().' T',
                    'limit' => 1,
                ]);
        } else {
            throw new Exception(Application::getLang()->translate('Bad arguments'), 500, Exception::BADARGUMENTS);
        }

        $stmt = $model->getDb()->prepare($query->toSql());
        $stmt->execute($query->params);
        $record = $stmt->fetchObject(get_class($model), [false]);
        $stmt->closeCursor();

        return $record;
    }

    /**
     * @param Query|array|bool $args
     *
     * @return array
     *
     * @throws Exception
     */
    public static function findAll($args = false)
    {
        /* @var ActiveRecord $model */

        $query = null;
        $model = new static();

        if ($args && $args instanceof Query) {
            $query = $args;
            if (empty($query->from)) {
                $query->addFrom($model->getTableName().' T');
            }
        } elseif ($args && is_array($args)) {
            if (!isset($args['from'])) {
                $args['from'] = $model->getTableName().' T';
            }
            $query = new Query($args);
        } elseif (!$args) {
            $query = new Query(
                [
                    'from' => $model->getTableName().' T',
                ]);
        } else {
            throw new Exception(Application::getLang()->translate('Bad arguments'), 500, Exception::BADARGUMENTS);
        }

        $stmt = $model->getDb()->prepare($query->toSql());
        $stmt->execute($query->params);
        $records = [];

        while ($record = $stmt->fetchObject(get_class($model), [false])) {
            $records[] = $record;
        }

        $stmt->closeCursor();

        return $records;
    }

    /**
     * @param Query|array|bool $args
     *
     * @return int
     *
     * @throws Exception
     */
    public static function count($args = false)
    {
        /* @var ActiveRecord $model */

        $query = null;
        $model = new static();

        if ($args && $args instanceof Query) {
            $query = $args;
            if (empty($query->from)) {
                $query->addFrom($model->getTableName().' T');
            }
            $query->select = 'COUNT(*)';
        } elseif ($args && is_array($args)) {
            if (!isset($args['from'])) {
                $args['from'] = $model->getTableName().' T';
            }
            $query = new Query($args);
            $query->select = 'COUNT(*)';
        } elseif (!$args) {
            $query = new Query(
                [
                    'from' => $model->getTableName().' T',
                    'select' => 'COUNT(*)',
                ]);
        } else {
            throw new Exception(Application::getLang()->translate('Bad arguments'), 500, Exception::BADARGUMENTS);
        }

        $stmt = $model->getDb()->prepare($query->toSql());
        $stmt->execute($query->params);
        $records = $stmt->fetchColumn();

        $stmt->closeCursor();

        return $records;
    }

    /**
     * @param array $data
     */
    public function bind($data = [])
    {
        foreach ($data as $attribute => $value) {
            if (property_exists($this, $attribute)) {
                $this->setDirty();
                $this->{$attribute} = $value;
            }
        }
    }

    /**
     * @return bool
     */
    protected function update()
    {
        if ($this->isDirty()) {
            $sql = 'UPDATE '.$this->getTableName().' SET ';
            $data = [];
            $params = [];
            $pk_name = $this->getPkName();

            foreach ((new \ReflectionObject($this))->getProperties(\ReflectionProperty::IS_PUBLIC) as $property) {
                $is_pk = false;

                if (is_array($pk_name)) {
                    foreach ($pk_name as $pk) {
                        $is_pk = $property->getName() == $pk;
                        if ($is_pk) {
                            break;
                        }
                    }
                } else {
                    $is_pk = $property->getName() == $pk_name;
                }

                if ($is_pk) {
                    continue;
                }

                if ($this->{$property->getName()} instanceof Expression) {
                    $data[] = "`{$property->getName()}` = ".$this->{$property->getName()}->toSql();
                } else {
                    $data[] = "`{$property->getName()}` = :{$property->getName()}";
                    $params[":{$property->getName()}"] = $this->{$property->getName()};
                }
            }

            $sql .= implode(',', $data);
            $pk_condition = null;
            $stmt = null;

            if (is_array($pk_name)) {
                foreach ($pk_name as $pk) {
                    $pk_condition[] = "{$pk} = :_{$pk}";
                    $params[":_{$pk}"] = $this->{$pk};
                }
                $pk_condition = implode(' AND ', $pk_condition);
            } else {
                $pk_condition = "`{$pk_name}` = :_{$pk_name}";
                $params[":_{$pk_name}"] = $this->{$pk_name};
            }

            $sql .= " WHERE {$pk_condition}";

            $stmt = $this->getDb()->prepare($sql);
            $stmt->execute($params);

            $this->setDirty(false);
            $stmt->closeCursor();
        }

        return true;
    }

    protected function insert()
    {
        if ($this->isDirty()) {
            $sql = 'INSERT INTO '.$this->getTableName().'  SET ';
            $data = [];
            $params = [];

            $pk_name = $this->getPkName();

            foreach ((new \ReflectionObject($this))->getProperties(\ReflectionProperty::IS_PUBLIC) as $property) {
                if ($this->{$property->getName()}) {
                    if ($this->{$property->getName()} instanceof Expression) {
                        $data[] = "`{$property->getName()}` = ".$this->{$property->getName()}->toSql();
                    } else {
                        $data[] = "`{$property->getName()}` = :{$property->getName()}";
                        $params[":{$property->getName()}"] = $this->{$property->getName()};
                    }
                }
            }

            $sql .= implode(',', $data);

            $stmt = $this->getDb()->prepare($sql);
            $stmt->execute($params);
            $last_insert_id = $this->getDb()->lastInsertId();

            if (is_array($pk_name)) {
                /*
                 * @TODO: Composite PK
                 */
            } else {
                $this->{$pk_name} = $last_insert_id;
            }

            $this->setDirty(false);
            $stmt->closeCursor();
            $this->_is_new = false;
        }

        return true;
    }

    public function delete()
    {
        $sql = 'DELETE FROM '.$this->getTableName().' ';

        $pk_name = $this->getPkName();
        $params = [];

        if (is_array($pk_name)) {
            $pk_condition = [];
            foreach ($pk_name as $pk) {
                $pk_condition[] = "{$pk} = :_{$pk}";
                $params[":_{$pk}"] = $this->{$pk};
            }
            $pk_condition = implode(' AND ', $pk_condition);
        } else {
            $pk_condition = "`{$pk_name}` = :_{$pk_name}";
            $params[":_{$pk_name}"] = $this->{$pk_name};
        }

        $sql .= " WHERE {$pk_condition}";

        $stmt = $this->getDb()->prepare($sql);
        $stmt->execute($params);

        $stmt->closeCursor();

        return true;
    }

    /**
     * @param bool $validate
     *
     * @return bool
     */
    public function save($validate = true)
    {
        $success = true;

        if ($validate) {
            if ($success = $this->validate()) {
                if ($this->isNew()) {
                    $this->insert();
                } else {
                    $this->update();
                }
            }
        } else {
            if ($this->isNew()) {
                $this->insert();
            } else {
                $this->update();
            }
        }

        return $success;
    }

    public function afterFind()
    {
    }
}
