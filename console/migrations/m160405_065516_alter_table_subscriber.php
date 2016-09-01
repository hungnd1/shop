<?php

use yii\db\Migration;

class m160405_065516_alter_table_subscriber extends Migration
{
    public function up()
    {
        $this->dropColumn("subscriber",'password');
        $this->addColumn("subscriber",'auth_key','VARCHAR(32) NOT NULL AFTER `full_name`');
        $this->addColumn("subscriber",'password_hash','VARCHAR(255) NOT NULL AFTER `auth_key`');
    }

    public function down()
    {
        echo "m160405_065516_alter_table_subscriber cannot be reverted.\n";

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
