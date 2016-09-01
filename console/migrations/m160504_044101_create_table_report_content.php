<?php

use yii\db\Migration;

class m160504_044101_create_table_report_content extends Migration
{
    public function up()
    {
        $sql =<<<SQL
CREATE TABLE `report_content` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `report_date` INT(11) NULL,
  `site_id` INT(11) NULL,
  `content_type` INT(11) NULL,
  `category_id` INT(11) NULL,
  `total_content` INT(11) NULL,
  `count_content_upload_daily` INT(11) NULL,
  `total_content_view` INT(11) NULL,
  `total_content_buy` INT(11) NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_report_content_report_date_idx` (`report_date` ASC),
  INDEX `fk_report_content_site_id_idx` (`site_id` ASC),
  INDEX `fk_report_content_category_id_idx` (`category_id` ASC),
  CONSTRAINT `fk_report_content_site_id`
    FOREIGN KEY (`site_id`)
    REFERENCES `site` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_report_content_category_id`
    FOREIGN KEY (`category_id`)
    REFERENCES `category` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
COMMENT = 'Bảng báo cáo nội dung'
SQL;
        $this->execute($sql);
    }

    public function down()
    {
        $this->dropTable('table_report_content');
    }
}
