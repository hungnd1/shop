<?php

use yii\db\Migration;

class m160608_042001_alter_table_subscriber_service_asm_column_service_name extends Migration
{
    public function up()
    {
        $sql =<<<SQL
ALTER TABLE `subscriber_service_asm`
CHANGE COLUMN `service_name` `service_name` VARCHAR(200) CHARACTER SET 'utf8' NULL
SQL;
        $this->execute($sql);
    }

    public function down()
    {
        echo "m160608_042001_alter_table_subscriber_service_asm_column_service_name cannot be reverted.\n";

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
