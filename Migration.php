<?php
/**
 * User: execut
 * Date: 20.05.16
 * Time: 14:49
 */

namespace execut\yii\migration;

use yii\db\ColumnSchemaBuilder;

abstract class Migration extends \yii\db\Migration
{
    use MigrationTrait;
    /**
     * @var Inverter
     */
    public $inverter = null;
    public $isSafe = true;
    public function init() {
        parent::init();
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
}