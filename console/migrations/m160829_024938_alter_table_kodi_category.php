<?php

use yii\db\Migration;

class m160829_024938_alter_table_kodi_category extends Migration
{
    public function up()
    {
        $this->addColumn('kodi_category', 'parent', 'VARCHAR(20)');

    }

    public function down()
    {
        echo "m160829_024938_alter_table_kodi_category cannot be reverted.\n";

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
