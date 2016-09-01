<?php

use yii\db\Migration;

class m160829_092740_add_column_view_count_table_content_view_log extends Migration
{
    public function up()
    {
        $sql = <<<SQL
ALTER TABLE `content_view_log`
ADD COLUMN `view_count` INT(11) NULL DEFAULT 1 AFTER `view_date`;
SQL;
        $this->execute($sql);
    }

    public function down()
    {
        echo "m160829_092740_add_column_view_count_table_content_view_log cannot be reverted.\n";

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
