<?php
/**
 * User: execut
 * Date: 20.05.16
 * Time: 14:49
 */

namespace execut\yii\migration;

use yii\db\ColumnSchemaBuilder;
use yii\db\Connection;
use yii\di\Instance;

abstract class Migration extends \yii\db\Migration
{
    use MigrationTrait;
    /**
     * @var Inverter
     */
    public $inverter = null;
    protected $isRefreshSchema = true;
    public $isSafe = true;

    public function init() {
        $this->initDb();

        $this->inverter =   new Inverter([
            'migration' => $this,
        ]);
        $this->initInverter($this->inverter);
    }

    /**
     * @param Inverter $inverter
     * @return mixed
     */
    abstract public function initInverter(Inverter $i);

    public function safeUp() {
        if ($this->isSafe) {
            return $this->inverter->up();
        }
    }

    public function safeDown() {
        if ($this->isSafe) {
            return $this->inverter->down();
        }
    }

    public function defaultColumns($otherColumns = []) {
        $standardColumns = \yii::createObject([
            'class' => StandardColumns::class,
            'migration' => $this,
            'otherColumns' => $otherColumns,
        ]);

        return $standardColumns->getColumns();
    }

    /**
     * @todo Incompatible with other schemas
     *
     * @return ColumnSchemaBuilder
     */
    public function data() {
        return $this->getDb()->getSchema()->createColumnSchemaBuilder('bytea');
    }

    protected function initDb()
    {
        $this->db = Instance::ensure($this->db, Connection::className());
        if ($this->isRefreshSchema) {
            $this->db->getSchema()->refresh();
        }

        $this->db->enableSlaves = false;
    }
}