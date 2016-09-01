<?php

use yii\db\Migration;

class m160425_072351_create_table_report_subscriber_activity extends Migration
{
    public function up()
    {
        $sql =<<<SQL
CREATE TABLE `report_subscriber_activity` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `report_date` INT(11) NULL,
  `site_id` INT(11) NOT NULL,
  `via_site` INT(11) NULL,
  `view_content` INT(11) NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_report_subscriber_activity_site_id_idx` (`site_id` ASC),
  CONSTRAINT `fk_report_subscriber_activity_site_id`
    FOREIGN KEY (`site_id`)
    REFERENCES `site` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
COMMENT = 'Bảng thống kê lượt truy cập dịch vụ/ có xem nội dung'
SQL;
        $this->execute($sql);
    }

    public function down()
    {
        $this->dropTable('report_subscriber_activity');
    }
}
