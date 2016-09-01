<?php

use yii\db\Migration;

class m160810_075614_alter_table_migrate_status extends Migration
{
    public function up()
    {
        $this->dropColumn('migrate_status', 'last_migrated_at');
        $this->dropColumn('migrate_status', 'last_data_at');
        $this->addColumn('migrate_status', 'started_at', 'int');
        $this->addColumn('migrate_status', 'finished_at', 'int');
        $this->addColumn('migrate_status', 'status', 'int');
    }

    public function down()
    {
        echo "m160810_075614_alter_table_migrate_status cannot be reverted.\n";

        return false;
    }

    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
