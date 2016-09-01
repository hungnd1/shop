<?php

use yii\db\Migration;

class m160505_065714_alter_table_content_view_log extends Migration
{
    public function up()
    {
        $sql =<<<SQL
ALTER TABLE `content_view_log`
ADD COLUMN `category_id` INT(11) NULL AFTER `content_id`,
ADD INDEX `idx_content_view_log_category_id` (`category_id` ASC);
ALTER TABLE `content_view_log`
ADD CONSTRAINT `fk_content_view_log_category_id`
  FOREIGN KEY (`category_id`)
  REFERENCES `category` (`id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION;
SQL;
        $this->execute($sql);
    }

    public function down()
    {
        echo "m160505_065714_alter_table_content_view_log cannot be reverted.\n";

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
