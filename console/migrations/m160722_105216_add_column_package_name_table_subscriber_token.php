<?php

use yii\db\Migration;

class m160722_105216_add_column_package_name_table_subscriber_token extends Migration
{
    public function up()
    {

$sql = <<<SQL
ALTER TABLE `subscriber_token`
ADD COLUMN `package_name` VARCHAR(255) NOT NULL AFTER `subscriber_id`;
SQL;
$this->execute($sql);

    }

    public function down()
    {
        echo "m160722_105216_add_column_package_name_table_subscriber_token cannot be reverted.\n";

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
