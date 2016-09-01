<?php

use yii\db\Migration;

class m160726_072758_add_idx_status_table_content_site_asm extends Migration
{
    public function up()
    {
        $sql = <<<SQL
ALTER TABLE `content_site_asm`
ADD INDEX `idx_content_site_asm_status` (`status` ASC);
SQL;
        $this->execute($sql);
    }

    public function down()
    {
        echo "m160726_072758_add_idx_status_table_content_site_asm cannot be reverted.\n";

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
