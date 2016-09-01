<?php

use yii\db\Migration;

class m160617_071159_add_column_status_table_content_site_asm extends Migration
{
    public function up()
    {
        $sql = <<<SQL
ALTER TABLE `content_site_asm`
ADD COLUMN `status` INT(11) NULL DEFAULT 10 AFTER `site_id`
SQL;
        $this->execute($sql);
    }

    public function down()
    {
        echo "m160617_071159_add_column_status_table_content_site_asm cannot be reverted.\n";

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
