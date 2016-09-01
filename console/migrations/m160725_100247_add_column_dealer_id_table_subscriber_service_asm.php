<?php

use yii\db\Migration;

class m160725_100247_add_column_dealer_id_table_subscriber_service_asm extends Migration
{
    public function up()
    {
        $sql = <<<SQL
ALTER TABLE `subscriber_service_asm`
ADD COLUMN `dealer_id` INT(11) NULL AFTER `site_id`,
ADD INDEX `idx_subscriber_service_asm_dealer_id` (`dealer_id` ASC);
ALTER TABLE `subscriber_service_asm`
ADD CONSTRAINT `fk_subscriber_service_asm_dealer_id`
  FOREIGN KEY (`dealer_id`)
  REFERENCES `dealer` (`id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION;

SQL;
        $this->execute($sql);
    }

    public function down()
    {
        echo "m160725_100247_add_column_dealer_id_table_subscriber_service_asm cannot be reverted.\n";

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
