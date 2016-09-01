<?php

use yii\db\Migration;

class m160506_080551_create_table_report_revenues extends Migration
{
    public function up()
    {
        $sql =<<<SQL
CREATE TABLE `report_revenue` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `report_date` INT(11) NOT NULL ,
  `site_id` INT(11) NOT NULL,
  `service_id` INT(11) NULL,
  `total_revenues` DOUBLE DEFAULT '0',
  `renew_revenues` DOUBLE DEFAULT '0',
  `register_revenues` DOUBLE DEFAULT '0',
  `content_buy_revenues` DOUBLE DEFAULT '0',
  PRIMARY KEY (`id`),
  INDEX `idx_report_revenue_report_date` (`report_date` ASC),
  INDEX `idx_report_revenue_site_id` (`site_id` ASC),
  INDEX `idx_report_revenue_service_id` (`service_id` ASC),
  CONSTRAINT `fk_report_revenue_site_id`
    FOREIGN KEY (`site_id`)
    REFERENCES `site` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_report_revenue_service_id`
    FOREIGN KEY (`service_id`)
    REFERENCES `service` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
COMMENT = 'Bảng thống kê doanh thu'
SQL;
        $this->execute($sql);
    }

    public function down()
    {
        $this->dropTable('table_report_revenues');
    }
}
