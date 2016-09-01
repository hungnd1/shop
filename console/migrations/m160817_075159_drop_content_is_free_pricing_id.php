<?php

use yii\db\Migration;

class m160817_075159_drop_content_is_free_pricing_id extends Migration
{
    public function up()
    {

$sql =<<<SQL
ALTER TABLE `content` 
DROP FOREIGN KEY `fk_content_pricing1`;
ALTER TABLE `content` 
DROP COLUMN `is_free`,
DROP COLUMN `pricing_id`,
DROP INDEX `fk_content_pricing1_idx` ,
DROP INDEX `idx_is_free` ;
SQL;
        $this->execute($sql);
    }

    public function down()
    {
        echo "m160817_075159_drop_content_is_free_pricing_id cannot be reverted.\n";

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
