<?php
/**
 * User: execut
 * Date: 20.05.16
 * Time: 14:49
 */

namespace execut\yii\migration;


use yii\base\Exception;
use yii\db\mysql\Schema;
use yii\helpers\ArrayHelper;

trait MigrationTrait
{
    public function table($name) {
        return new Table([
            'name' => $name,
            'migration' => $this,
        ]);
    }

    public function addColumns($table, $columns) {
        foreach ($columns as $name => $type) {
            $this->addColumn($table, $name, $type);
        }
    }

    public function dropColumns($table, $columns) {
        foreach ($columns as $name => $type) {
            $this->dropColumn($table, $name);
        }
    }

    public function alterColumnSetDefault($table, $column, $value) {
        $this->alterColumnSet($table, $column, 'DEFAULT ' . $value);
    }

    public function alterColumnDropDefault($table, $column) {
        $this->alterColumnDrop($table, $column, 'DEFAULT');
    }

    public function alterColumnSetNotNull($table, $column) {
        if ($this->isMysql()) {
            /**
             * @var Schema $schema
             */
            $schema = $this->db->schema;
            $columnType = $schema->getTableSchema($table)->getColumn($column)->dbType . ' NOT NULL';
            $this->execute('ALTER TABLE ' . $table . ' MODIFY COLUMN ' . $column . ' ' . $columnType);
        } else {
            $this->alterColumnSet($table, $column, 'NOT NULL');
        }
    }

    public function alterColumnDropNotNull($table, $column) {

        if ($this->isMysql()) {
            /**
             * @var Schema $schema
             */
            $schema = $this->db->schema;
            $columnType = $schema->getTableSchema($table)->getColumn($column)->dbType;
            $this->execute('ALTER TABLE ' . $table . ' MODIFY COLUMN ' . $column . ' ' . $columnType);
        } else {
            $this->alterColumnDrop($table, $column, 'NOT NULL');
        }
    }

    public function alterColumnSet($table, $column, $operation) {
        $this->execute('ALTER TABLE ' . $table . ' ALTER COLUMN ' . $column . ' SET ' . $operation);
    }

    /**
     * @return bool
     */
    protected function isMysql()
    {
        return $this->db->driverName === 'mysql';
    }

    public function alterColumnDrop($table, $column, $operation) {
        $this->execute('ALTER TABLE ' . $table . ' ALTER COLUMN ' . $column . ' DROP ' . $operation);
    }

    public function createProcedureTrigger($triggerName, $table, $procedure, $isBeforeUpdate = false, $isBeforeInsert = false, $isBeforeDelete = false) {
        $before = [];
        if ($isBeforeUpdate) {
            $before[] = 'UPDATE';
        }

        if ($isBeforeInsert) {
            $before[] = 'INSERT';
        }

        if ($isBeforeInsert) {
            $before[] = 'DELETE';
        }

        if (strpos($procedure, '(') === false) {
            $procedure .= '()';
        }

        $before = implode(' OR ', $before);

        $sql = <<<SQL
CREATE TRIGGER $triggerName
  BEFORE $before
  ON $table
  FOR EACH ROW
  EXECUTE PROCEDURE $procedure;
SQL;

        $this->execute($sql);
    }

    public function dropProcedureTrigger($triggerName, $table) {
        $sql = <<<SQL
DROP TRIGGER $triggerName
  ON $table
SQL;
        $this->execute($sql);
    }

    public function createTrigger($table, $name, $procedure, $arguments = null, $events = ['insert']) {
        if (empty($arguments)) {
            $arguments = [];
        } else if (is_string($arguments)) {
            $arguments = [$arguments];
        }

        $procedure = $procedure . '(' . implode(',', $arguments) . ')';
        $before = implode(' OR ', $events);
        $sql = <<<SQL
CREATE TRIGGER $name BEFORE $before ON $table
  FOR EACH ROW EXECUTE PROCEDURE $procedure
SQL;

        $this->execute($sql);
    }

    public function dropTrigger($table, $name) {
        $sql = <<<SQL
DROP TRIGGER $name
  ON $table
SQL;
        $this->execute($sql);
    }
    
    public function batchDelete($table, $columns, $values) {
        if (empty($columns) || !in_array('id', $columns)) {
            throw new Exception('Primary key required for delete');
        }

        $ids = ArrayHelper::map($values, '0', '0');

        $this->delete($table, [
            'id' => $ids,
        ]);

        return $this;
    }
}