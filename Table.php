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

    public function addColumns($columns)
    {
        $this->migration->addColumns($this->name, $columns);
        return $this;
    }

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

    public function getColumnNameFromTable($toTable) {
        if (substr($toTable, -1) === 's') {
            $columnName = substr($toTable, 0, strlen($toTable) - 1);
        } else {
            $columnName = $toTable;
        }

        $columnName .= '_id';
        return $columnName;
    }

    public function addForeignColumn($toTable, $isNotNull = false, $defaultValue = null, $columnName = null, $columnType = null, $refColumn = 'id', $fkName = null)
    {
        if ($columnName === null) {
            $columnName = $this->getColumnNameFromTable($toTable);
        }

        if ($fkName === null) {
            $fkName = $this->generateFkName($columnName);
        }

        if ($columnType === null) {
            $columnType = $this->migration->integer();
        }

        if ($isNotNull && $this->isMysql()) {
            $columnType .= ' NOT NULL';
        }

        $this->migration->addColumn($this->name, $columnName, $columnType);
        $this->migration->addForeignKey($fkName, $this->name, $columnName, $toTable, $refColumn);
        if ($defaultValue !== null) {
            $this->migration->update($this->name, [
                $columnName => $defaultValue,
            ]);
        }

        if ($isNotNull && !$this->isMysql()) {
            $this->migration->alterColumnSetNotNull($this->name, $columnName);
        }

        return $this;
    }

    public function dropForeignColumn($toTable, $isNotNull = false, $defaultValue = null, $columnName = null, $columnType = null, $refColumn = 'id', $fkName = null)
    {
        if ($columnName === null) {
            $columnName = $this->getColumnNameFromTable($toTable);
        }

        if ($fkName === null) {
            $fkName = $this->generateFkName($columnName);
        }

        if ($columnType === null) {
            $columnType = $this->migration->integer();
        }

        if ($isNotNull && $this->isMysql()) {
            $columnType .= ' NOT NULL';
        }

        $columnName = substr($toTable, 0, strlen($toTable) - 1) . '_id';

        if ($this->isMysql()) {
            $this->migration->dropForeignKey($fkName, $this->name, $columnName, $toTable, $refColumn);
        }

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
        $this->migration->createTable($this->name, $columns, $this->getOptions());

        return $this;
    }

    protected $options = [];
    public function getOptions() {
        $tableOptions = '';
        if ($this->isMysql()) {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        return $tableOptions;
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

    public function rename($newName) {
        $this->migration->renameTable($this->name, $newName);
        $this->name = $newName;

        return $this;
    }

    public function renameColumn($oldName, $newName) {
        $this->migration->renameColumn($this->name, $oldName, $newName);

        return $this;
    }

    /**
     * @return bool
     */
    protected function isMysql()
    {
        return $this->migration->db->driverName === 'mysql';
    }

    /**
     * @param $columnName
     * @return string
     */
    protected function generateFkName($columnName)
    {
        $fkName = $this->name . '_' . $columnName . '_fk';
        return $fkName;
    }
}