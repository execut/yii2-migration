# yii2-migration
This is a typical migration for yii2:
```
    public function safeUp()
    {
        $this->createTable('characteristics_units', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull(),
            'short_name' => $this->string()->notNull(),
            'created' => $this->dateTime()->notNull()->defaultExpression('now()'),
            'updated' => $this->dateTime(),
        ]);

        $this->createTable('characteristics', [
            'id' => $this->primaryKey(),
            'characteristics_unit_id' => $this->integer()->notNull(),
            'name' => $this->string()->notNull(),
            'created' => $this->dateTime()->notNull()->defaultExpression('now()'),
            'updated' => $this->dateTime(),
        ]);

        $this->addForeignKey('characteristics_unit_id_characteristics_fk', 'characteristics', 'characteristics_unit_id', 'characteristics_units', 'id');
    }

    public function safeDown()
    {
        $this->dropTable('characteristics');
        $this->dropTable('characteristics_units');
    }
```
Why write more? If you use execut yii2-migration helper, you can write it faster and more compact:
```
    public function initInverter(\execut\yii\migration\Inverter $i)
    {
        $i->table('characteristics')->create(array_merge($this->defaultColumns(), [
            'name' => $this->string()->notNull(),
            'short_name' => $this->string()->notNull(),
        ]));

        $i->table('characteristics_units')->create(array_merge($this->defaultColumns(), [
            'name' => $this->string()->notNull(),
        ]))->addForeignColumn('characteristics');
    }
```
## Installation

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

### Install

Either run

```
$ php composer.phar require execut/yii2-migration "dev-master"
```

or add

```
"execut/yii2-migration": "dev-master"
```

to the ```require``` section of your `composer.json` file.

## Usage
To use my api, simply expand the migration class from the execut\yii\migration\Migration class and override the abstract method:
```
     public function initInverter(\execut\yii\migration\Inverter $i)
     {
     }
```
$i has all the methods that normal migration has, but allows you to write actions up and down at a time.

To permanently do not rewrite the migration, you can define a new template for the yii migrate\create command.
```
    'controllerMap' => [
        'migrate' => [
            'templateFile' => '@vendor/execut/yii2-migration/views/template.php',
        ],
```

## Supported databases
Currently only supported PostgreSQL and MySQL. Also you can use BDR plugin for PostgreSQL.
