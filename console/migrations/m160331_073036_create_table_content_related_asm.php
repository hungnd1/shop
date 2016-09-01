<?php

use yii\db\Migration;

class m160331_073036_create_table_content_related_asm extends Migration
{
    public function up()
    {
        $sql =<<<SQL
CREATE TABLE `content_related_asm` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `content_id` INT(11) NOT NULL,
  `content_related_id` INT(11) NOT NULL COMMENT 'Content liên quan',
  `updated_at` INT(11) NULL,
  `created_at` INT(11) NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_content_related_asm_content_id_idx` (`content_id` ASC),
  INDEX `fk_content_related_asm_content_related_id_idx` (`content_related_id` ASC),
  CONSTRAINT `fk_content_related_asm_content_id`
    FOREIGN KEY (`content_id`)
    REFERENCES `content` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_content_related_asm_content_related_id`
    FOREIGN KEY (`content_related_id`)
    REFERENCES `content` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
COMMENT = 'Bảng nội dung liên quan'
SQL;
        $this->execute($sql);
    }

    public function down()
    {
        $this->dropTable('table_content_related_asm');
    }
}
