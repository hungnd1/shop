<?php

use yii\db\Migration;

/**
 * Handles the creation for table `table_content_site_status_asm`.
 */
class m160613_085351_create_table_content_site_status_asm extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $sql = <<<SQL
CREATE TABLE `content_site_status_asm` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `content_id` INT NULL,
  `site_id` INT NULL,
  `status` INT NULL,
  `created_at` INT NULL,
  `updated_at` INT NULL,
  PRIMARY KEY (`id`),
  INDEX `index2` (`site_id` ASC),
  INDEX `fk_content_site_status_asm_1_idx` (`content_id` ASC),
  CONSTRAINT `fk_content_site_status_asm_1`
    FOREIGN KEY (`content_id`)
    REFERENCES `content` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_content_site_status_asm_2`
    FOREIGN KEY (`site_id`)
    REFERENCES `site` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION);
SQL;
        $this->execute($sql);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('content_site_status_asm');
    }
}
