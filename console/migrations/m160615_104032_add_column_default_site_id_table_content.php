<?php

use yii\db\Migration;

class m160615_104032_add_column_default_site_id_table_content extends Migration
{
    public function up()
    {
        $sql = <<<SQL
ALTER TABLE `content`
ADD COLUMN `default_site_id` INT(11)  NULL AFTER `origin_url`,
ADD INDEX `idx_content_default_site_id` (`default_site_id` ASC);
SQL;
        $this->execute($sql);
    }

    public function down()
    {
        echo "m160615_104032_add_column_default_site_id_table_content cannot be reverted.\n";

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
