<?php

use yii\db\Migration;

class m160817_074222_create_pricing_id_content_site_asm extends Migration
{
    public function up()
    {
        $sql =<<<SQL
ALTER TABLE `content_site_asm` 
DROP COLUMN `price_id`,
ADD COLUMN `pricing_id` INT(11) NULL AFTER `transfer_status`,
ADD INDEX `fk_content_site_asm_pricing_id_idx` (`pricing_id` ASC);
ALTER TABLE `content_site_asm` 
ADD CONSTRAINT `fk_content_site_asm_pricing_id`
  FOREIGN KEY (`pricing_id`)
  REFERENCES `pricing` (`id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION;
SQL;
        $this->execute($sql);
    }

    public function down()
    {
        echo "m160817_074222_create_pricing_id_content_site_asm cannot be reverted.\n";

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
