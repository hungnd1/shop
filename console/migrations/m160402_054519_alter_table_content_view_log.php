<?php

use yii\db\Migration;

class m160402_054519_alter_table_content_view_log extends Migration
{
    public function up()
    {
        $this->alterColumn('content_view_log', 'view_date', 'INT(11)');
    }

    public function down()
    {
        echo "m160402_054519_alter_table_content_view_log cannot be reverted.\n";

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
