<?php

use yii\db\Migration;

class m160726_072531_add_idx_type_table_content extends Migration
{
    public function up()
    {
        $sql = <<<SQL
ALTER TABLE `content`
ADD INDEX `idx_content_type` (`type` ASC);
SQL;
        $this->execute($sql);
    }

    public function down()
    {
        echo "m160726_072531_add_idx_table_content cannot be reverted.\n";

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
