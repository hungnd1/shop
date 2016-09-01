<?php

use yii\db\Migration;

/**
 * Handles the creation for table `table_price_content_site`.
 */
class m160603_085521_create_table_price_content_site extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $sql = <<<SQL
CREATE TABLE `price_content_site` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `price_id` INT NOT NULL,
  `content_id` INT NOT NULL,
  `site_id` INT NOT NULL,
  `created_at` INT NULL,
  `updated_at` INT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_price_content_site_1_idx` (`content_id` ASC),
  INDEX `fk_price_content_site_2_idx` (`site_id` ASC),
  INDEX `fk_price_content_site_3_idx` (`price_id` ASC),
  CONSTRAINT `fk_price_content_site_1`
    FOREIGN KEY (`content_id`)
    REFERENCES `content` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_price_content_site_2`
    FOREIGN KEY (`site_id`)
    REFERENCES `site` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_price_content_site_3`
    FOREIGN KEY (`price_id`)
    REFERENCES `pricing` (`id`)
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
        $this->dropTable('price_content_site');
    }
}
