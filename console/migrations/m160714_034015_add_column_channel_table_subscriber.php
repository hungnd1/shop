<?php

use yii\db\Migration;

class m160714_034015_add_column_channel_table_subscriber extends Migration
{
    public function up()
    {
        $sql = <<<SQL
ALTER TABLE `subscriber`
ADD COLUMN `channel` INT(11) NULL DEFAULT 7 AFTER `authen_type`
SQL;
        $this->execute($sql);
    }

    public function down()
    {
        echo "m160714_034015_add_column_channel_table_subscriber cannot be reverted.\n";

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
