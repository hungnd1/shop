<?php

use yii\db\Migration;

class m160802_081441_creat_item_kodi extends Migration
{
    public function up()
    {
        $sql =<<<SQL
CREATE TABLE `item_kodi` (
  `id` INT NOT NULL AUTO_INCREMENT COMMENT '',
  `display_name` VARCHAR(50) NOT NULL COMMENT '',
  `description` TEXT NULL COMMENT '',
  `image` VARCHAR(500) NOT NULL COMMENT '',
  `path` TEXT NOT NULL COMMENT '',
  `type` INT NULL COMMENT '',
  `file_download` VARCHAR(255) NULL COMMENT '',
  `status` INT NULL COMMENT '',
  `honor` INT NULL COMMENT '',
  `created_at` INT NULL COMMENT '',
  `updated_at` INT NULL COMMENT '',
  PRIMARY KEY (`id`)  COMMENT '')
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_bin


SQL;
        $this->execute($sql);

    }

    public function down()
    {
        echo "m160802_081441_creat_item_kodi cannot be reverted.\n";

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
