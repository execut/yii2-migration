<?php
/**
 */

namespace execut\yii\migration;


use yii\base\Component;

class StandardColumns extends Component
{
    public $migration = null;
    public $otherColumns = [];
    public function getColumns() {
        return array_merge([
            'id' => $this->migration->primaryKey(),
            'created' => $this->migration->dateTime()->notNull()->defaultExpression('now()'),
            'updated' => $this->migration->dateTime(),
        ], $this->otherColumns);
    }
}