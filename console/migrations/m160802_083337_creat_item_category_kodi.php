<?php

use yii\db\Migration;

class m160802_083337_creat_item_category_kodi extends Migration
{
    public function up()
    {
        $sql =<<<SQL
CREATE TABLE `kodi_category_item_asm` (
  `id` INT NOT NULL AUTO_INCREMENT COMMENT '',
  `category_id` INT NOT NULL COMMENT '',
  `item_id` INT NOT NULL COMMENT '',
  `created_at` INT NULL COMMENT '',
  `updated_at` INT NULL COMMENT '',
  PRIMARY KEY (`id`)  COMMENT '',
  INDEX `fk_category_asm_category_idx` (`category_id` ASC)  COMMENT '',
  INDEX `fk_category_asm_item_idx` (`item_id` ASC)  COMMENT '',
  CONSTRAINT `fk_category_asm_category`
    FOREIGN KEY (`category_id`)
    REFERENCES `kodi_category` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_category_asm_item`
    FOREIGN KEY (`item_id`)
    REFERENCES `item_kodi` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)



SQL;
        $this->execute($sql);
    }

    public function down()
    {
        echo "m160802_083337_creat_item_category_kodi cannot be reverted.\n";

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
