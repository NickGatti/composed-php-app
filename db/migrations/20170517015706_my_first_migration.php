<?php

use Phinx\Migration\AbstractMigration;

class MyFirstMigration extends AbstractMigration
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
        $details = $this->table('details');
        $details->addColumn('email', 'string', ['limit' => 64])
            ->addIndex('email', ['unique' => true])
            ->save();
        
        $users = $this->table('users');
        $users->addColumn('username', 'string', ['limit' => 16])
            ->addColumn('password', 'string', ['limit' => 32])
            ->addColumn('detail_id', 'integer')
            ->addIndex('username', ['unique' => true])
            ->addForeignKey('detail_id', 'details', 'id')
            ->save();
    }
}
