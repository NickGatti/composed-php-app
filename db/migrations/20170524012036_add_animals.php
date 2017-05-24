<?php

use Phinx\Migration\AbstractMigration;

class AddAnimals extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
     *
     * The following commands can be used in this method and Phinx will
     * automatically reverse them when rolling back:
     *
     *    createTable
     *    renameTable
     *    addColumn
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change()
    {
        $animals = $this->table('animals');
        $animals->addColumn('color', 'string', ['limit' => 8])
            ->addColumn('name', 'string', ['limit' => 64])
            ->addColumn('description', 'string', ['limit' => 1024])
            ->addColumn('image', 'string', ['limit' => 512])
            ->addColumn('wearable', 'boolean')
            ->addColumn('user_id', 'integer')
            ->addIndex(['color'])
            ->addIndex(['name'], ['unique' => true])
            ->addForeignKey('user_id', 'users', 'id')
            ->create();
    }
}
