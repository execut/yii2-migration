<?php
/**
 * Date: 20.05.16
 * Time: 14:38
 */

namespace execut\yii\migration;


use yii\base\Component;
use execut\yii\migration\Migration as M;

class Table extends Component
{
    public $name = null;
    /**
     * @var M
     */
    public $migration = null;

    public function addColumn($name, $type)
    {
        $this->migration->addColumn($this->name, $name, $type);
        return $this;
    }

    public function dropColumn($name, $type)
    {
        $this->migration->dropColumn($this->name, $name, $type);
        return $this;
    }

    public function alterColumnDropNotNull($name)
    {
        $this->migration->alterColumnDropNotNull($this->name, $name);
        return $this;
    }

    public function alterColumnDropDefault($name, $value)
    {
        $this->migration->alterColumnDropDefault($this->name, $name, $value);
        return $this;
    }

    public function alterColumnSetDefault($name, $value)
    {
        $this->migration->alterColumnSetDefault($this->name, $name, $value);
        return $this;
    }

    public function alterColumnSetNotNull($name)
    {
        $this->migration->alterColumnSetNotNull($this->name, $name);
        return $this;
    }

    public function addForeignColumn($toTable, $isNotNull = false, $defaultValue = null, $columnName = null)
    {
        if ($columnName === null) {
            $columnName = substr($toTable, 0, strlen($toTable) - 1) . '_id';
        }

        $this->migration->addColumn($this->name, $columnName, 'BIGINT');
        $this->migration->addForeignKey($this->name . '_' . $columnName . '_fk', $this->name, $columnName, $toTable, 'id');
        if ($isNotNull) {
            if ($defaultValue !== null) {
                $this->migration->update($this->name, [
                    $columnName => $defaultValue,
                ]);
            }

            $this->migration->alterColumnSetNotNull($this->name, $columnName);
        }

        return $this;
    }

    public function dropForeignColumn($toTable)
    {
        $columnName = substr($toTable, 0, strlen($toTable) - 1) . '_id';

        $this->migration->dropColumn($this->name, $columnName, 'BIGINT');

        return $this;
    }

    public function getDb()
    {
        return $this->migration->db;
    }

    public function delete($where = '')
    {
        $this->migration->delete($this->name, $where);

        return $this;
    }

    public function create($columns = [])
    {
        $this->migration->createTable($this->name, $columns);

        return $this;
    }

    public function setBdrSequence() {
        $this->migration->alterSequence($this->name . '_id_seq', 'USING bdr', 'USING local');
    }

    public function drop($columns = [])
    {
        $this->migration->dropTable($this->name, $columns);

        return $this;
    }

    public function batchInsert($columns, $values) {
        $this->migration->batchInsert($this->name, $columns, $values);
        return $this;
    }

    public function update($attributes, $condition = '', $params = []) {
        $this->migration->update($this->name, $attributes, $condition, $params);
        return $this;
    }

    public function createIndex($columns, $isUnique = false, $name = null) {
        if ($name === null) {
            $name = $this->generateIndexName($columns, $isUnique);
        }

        $this->migration->createIndex($this->name . '_' . $name, $this->name, $columns, $isUnique);

        return $this;
    }

    public function dropIndex($columns, $isUnique = false) {
        $name = $this->generateIndexName($columns, $isUnique);

        $this->migration->createIndex($name, $this->name, $column, $isUnique);

        return $this;
    }

    public function changeColumnType($column, $oldType, $newType) {
        $this->dropColumn($column, $oldType);
        $this->addColumn($column, $newType);

        return $this;
    }

    public function createTrigger($name, $procedure, $arguments = [], $events = ['insert'])
    {
        $this->migration->createTrigger($this->name, $name, $procedure, $arguments, $events);
        return $this;
    }

    public function dropTrigger($name, $procedure, $arguments = [], $events = ['insert'])
    {
        $this->migration->createTrigger($this->name, $name, $procedure, $arguments, $events);
        return $this;
    }

    public function createProcedureTrigger($procedure, $isBeforeUpdate = false, $isBeforeInsert = false, $isBeforeDelete = false)
    {
        $triggerName = $procedure;
        $this->migration->createProcedureTrigger($triggerName, $this->name, $procedure, $isBeforeUpdate, $isBeforeInsert, $isBeforeDelete);
        return $this;
    }

    /**
     * @param $columns
     * @param $isUnique
     * @return array
     */
    protected function generateIndexName($columns, $isUnique)
    {
        if (!is_array($columns)) {
            $columns = [$columns];
        }

        $name = implode('_', $columns);
        if ($isUnique) {
            $name .= '_uk';
        } else {
            $name .= '_i';
        }

        return $name;
    }
}