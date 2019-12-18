<?php
/**
 * User: execut
 * Date: 20.05.16
 * Time: 14:49
 */

namespace execut\yii\migration;


use yii\db\Connection;
use yii\helpers\ArrayHelper;
use yii\base\Component;
use yii\base\Exception;

class Inverter extends Component
{
    use MigrationTrait;
    protected $operations = [];
    /**
     * @var \yii\db\Migration;
     */
    public $migration = null;
    public function addOperation($type, $arguments) {
        $this->operations[] = [$type, $arguments];
        return $this;
    }

    public function execute($sql, $params = [])
    {
        $this->addOperation(__FUNCTION__, func_get_args());
        return $this;
    }

    public function executeBoth($sqlUp, $sqlDown) {
        $this->addOperation(__FUNCTION__, func_get_args());
        return $this;
    }

    public function callback($callbackUp, $callbackDown) {
        $this->addOperation(__FUNCTION__, func_get_args());
        return $this;
    }
    
    public function addColumn($table, $column, $type)
    {
        $this->addOperation(__FUNCTION__, func_get_args());
        return $this;
    }

    public function dropColumn($table, $column, $type)
    {
        $this->addOperation(__FUNCTION__, func_get_args());
        return $this;
    }

    public function addColumns($table, $columns)
    {
        $this->addOperation(__FUNCTION__, func_get_args());
        return $this;
    }

    public function dropColumns($table, $columns)
    {
        $this->addOperation(__FUNCTION__, func_get_args());
        return $this;
    }

    public function addForeignKey($name, $table, $columns, $refTable, $refColumns, $delete = null, $update = null) {
        $this->addOperation(__FUNCTION__, func_get_args());
        return $this;
    }

    public function dropForeignKey($name, $table, $columns, $refTable, $refColumns, $delete = null, $update = null) {
        $this->addOperation(__FUNCTION__, func_get_args());
        return $this;
    }

    public function delete($table, $where = '', $params = []) {
        $this->addOperation(__FUNCTION__, func_get_args());
        return $this;
    }

    public function renameTable($oldTable, $newTable) {
        $this->addOperation(__FUNCTION__, func_get_args());
        return $this;
    }

    public function renameColumn($oldTable, $oldName, $newName) {
        $this->addOperation(__FUNCTION__, func_get_args());
        return $this;
    }

    public function up() {
        foreach ($this->operations as $operation) {
            call_user_func_array([$this->migration, $operation[0]], $operation[1]);
        }
    }

    public function alterColumnSetDefault($table, $column, $value) {
        $this->addOperation(__FUNCTION__, func_get_args());
        return $this;
    }

    public function alterColumnDropDefault($table, $column, $value) {
        $this->addOperation(__FUNCTION__, func_get_args());
        return $this;
    }

    public function alterColumnSetNotNull($table, $column) {
        $this->addOperation(__FUNCTION__, func_get_args());
        return $this;
    }

    public function alterColumnDropNotNull($table, $column) {
        $this->addOperation(__FUNCTION__, func_get_args());
        return $this;
    }

    public function createTable($table, $columns, $options = null) {
        $this->addOperation(__FUNCTION__, func_get_args());
        return $this;
    }

    public function dropTable($table, $columns = []) {
        $this->addOperation(__FUNCTION__, func_get_args());
        return $this;
    }

    public function createTrigger($table, $name, $procedure, $arguments = [], $events = ['insert']) {
        $this->addOperation(__FUNCTION__, func_get_args());
        return $this;
    }

    public function dropTrigger($table, $name, $procedure, $arguments = [], $events = ['insert']) {
        $this->addOperation(__FUNCTION__, func_get_args());
        return $this;
    }

    public function createProcedureTrigger($triggerName, $table, $procedure, $isBeforeUpdate = true, $isBeforeInsert = false) {
        $this->addOperation(__FUNCTION__, func_get_args());
        return $this;
    }

    public function dropProcedureTrigger($triggerName, $table, $procedure, $isBeforeUpdate = true, $isBeforeInsert = false) {
        $sql = <<<SQL
DROP TRIGGER $triggerName
  ON $table
SQL;

    }

    public function createIndex($name, $table, $columns, $unique = false) {
        $this->addOperation(__FUNCTION__, func_get_args());

        return $this;
    }

    public function dropIndex($name, $table, $columns, $unique = false) {
        $this->addOperation(__FUNCTION__, func_get_args());

        return $this;
    }

    public function batchInsert($table, $columns, $values) {
        $this->addOperation(__FUNCTION__, func_get_args());

        return $this;
    }

    public function batchDelete($table, $columns, $values) {
        $this->addOperation(__FUNCTION__, func_get_args());

        return $this;
    }

    public function update($table, $attributes, $condition = '', $params = []) {
        $this->addOperation(__FUNCTION__, func_get_args());

        return $this;
    }

    public function down() {
        foreach (array_reverse($this->operations) as $operation) {
            $function = $operation[0];
            switch ($function) {
                case 'addColumn':
                    $function = 'dropColumn';
                break;
                case 'dropColumn':
                    $function = 'addColumn';
                break;
                case 'update':
                    $function = 'update';
                break;
                case 'addForeignKey':
                    $function = 'dropForeignKey';
                break;
                case 'dropForeignKey':
                    $function = 'addForeignKey';
                break;
                case 'alterColumnSetDefault':
                    $function = 'alterColumnDropDefault';
                break;
                case 'alterColumnDropDefault':
                    $function = 'alterColumnSetDefault';
                break;
                case 'alterColumnSetNotNull':
                    $function = 'alterColumnDropNotNull';
                break;
                case 'alterColumnDropNotNull':
                    $function = 'alterColumnSetNotNull';
                break;
                case 'createTable':
                    $function = 'dropTable';
                break;
                case 'dropTable':
                    $function = 'createTable';
                break;
                case 'addColumns':
                    $function = 'dropColumns';
                break;
                case 'dropColumns':
                    $function = 'addColumns';
                break;
                case 'createProcedureTrigger':
                    $function = 'dropProcedureTrigger';
                break;
                case 'dropProcedureTrigger':
                    $function = 'createProcedureTrigger';
                break;
                case 'batchInsert':
                    $function = 'batchDelete';
                break;
                case 'batchDelete':
                    $function = 'batchInsert';
                break;
                case 'createIndex':
                    $function = 'dropIndex';
                break;
                case 'dropIndex':
                    $function = 'createIndex';
                break;
                case 'createTrigger':
                    $function = 'dropTrigger';
                break;
                case 'dropTrigger':
                    $function = 'createTrigger';
                break;
                case 'execute':
                    $function = 'execute';
                break;
                case 'executeBoth':
                    $function = 'execute';
                    $oldSql = $operation[1][0];
                    $operation[1][0] = $operation[1][1];
                    $operation[1][1] = $oldSql;
                break;
                case 'callback':
                    $function = 'callback';
                    $oldSql = $operation[1][0];
                    $operation[1][0] = $operation[1][1];
                    $operation[1][1] = $oldSql;
                break;
                case 'renameTable':
                    $oldTable = $operation[1][0];
                    $operation[1][0] = $operation[1][1];
                    $operation[1][1] = $oldTable;
                break;
                case 'renameColumn':
                    $oldTable = $operation[1][1];
                    $operation[1][1] = $operation[1][2];
                    $operation[1][2] = $oldTable;
                break;
            }

            if ($function) {
                call_user_func_array([$this->migration, $function], $operation[1]);
            }
        }
    }

    public function __call($name, $params)
    {
        return call_user_func_array([$this->migration, $name], $params);
    }

    /**
     * @return Connection
     */
    public function getDb() {
        return $this->migration->db;
    }
}