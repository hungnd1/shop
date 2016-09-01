<?php

use yii\db\Migration;

class m160406_114036_create_table_category_site_asm extends Migration
{
    public function up()
    {
        $sql =<<<SQL
CREATE TABLE `category_site_asm` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `category_id` INT(11) NOT NULL,
  `site_id` INT(11) NOT NULL,
  `updated_at` INT(11) NULL,
  `created_at` INT(11) NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_category_site_asm_site_id_idx` (`site_id` ASC),
  INDEX `fk_category_site_asm_category_id_idx` (`category_id` ASC),
  CONSTRAINT `fk_category_site_asm_site_id`
    FOREIGN KEY (`site_id`)
    REFERENCES `site` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_category_site_asm_category_id`
    FOREIGN KEY (`category_id`)
    REFERENCES `category` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
COMMENT = 'Bảng nội dung liên quan'
SQL;
        $this->execute($sql);
    }

    public function down()
    {
        $this->dropTable('table_category_site_asm');
    }
}
