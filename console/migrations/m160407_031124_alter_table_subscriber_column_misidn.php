<?php

use yii\db\Migration;

class m160407_031124_alter_table_subscriber_column_misidn extends Migration
{
    public function up()
    {
        $this->alterColumn("subscriber",'msisdn','VARCHAR(45)  AFTER `dealer_id`');
    }

    public function down()
    {
        echo "m160407_031124_alter_table_subscriber_column_misidn cannot be reverted.\n";

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
