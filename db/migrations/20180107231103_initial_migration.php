<?php

use Phinx\Migration\AbstractMigration;

class InitialMigration extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
     *
     */
    public function up()
    {
        $exists = $this->hasTable('users');
        if ($exists) {
            return;
        }

        $table = $this->table('users');
        $table
            ->addColumn('username', 'string')
            ->addColumn('email', 'string')
            ->addColumn('password', 'char', ['limit' => 60])
            ->addIndex(['username', 'email'], ['unique' => true])
            ->addTimestamps()
            ->create();
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        $this->dropTable('users');
    }
}
