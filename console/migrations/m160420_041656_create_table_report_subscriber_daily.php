<?php

use yii\db\Migration;

class m160420_041656_create_table_report_subscriber_daily extends Migration
{
    public function up()
    {
        $sql =<<<SQL
CREATE TABLE `report_subscriber_daily` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `report_date` INT(11) NULL,
  `count_subscriber` INT(11) NULL,
  `count_un_subscriber` INT(11) NULL,
  `site_id` INT(11) NOT NULL,
  `service_id` INT(11) NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_report_subscriber_daily_site_id_idx` (`site_id` ASC),
  INDEX `fk_report_subscriber_daily_service_id_idx` (`service_id` ASC),
  CONSTRAINT `fk_report_subscriber_daily_site_id`
    FOREIGN KEY (`site_id`)
    REFERENCES `site` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_report_subscriber_daily_service_id`
    FOREIGN KEY (`service_id`)
    REFERENCES `service` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
    COMMENT = 'Bảng thống kê lượt đăng ký / hủy'
SQL;
        $this->execute($sql);
    }

    public function down()
    {
        $this->dropTable('table_report_subscriber_daily');
    }
}
