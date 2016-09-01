<?php

use yii\db\Migration;

class m160428_033630_alter_table_report_subscriber_daily extends Migration
{
    public function up()
    {
        $this->dropTable('report_subscriber_daily');
        $sql =<<<SQL
CREATE TABLE `report_subscriber_daily` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `report_date` int(11) NOT NULL,
  `site_id` int(11) NOT NULL,
  `service_id` int(11) DEFAULT NULL,
  `total_subscriber` int(11) DEFAULT NULL,
  `total_active_subscriber` int(11) DEFAULT NULL,
  `subscriber_register_daily` int(11) DEFAULT NULL,
  `total_cancel_subscriber` int(11) DEFAULT NULL,
  `subscriber_cancel_daily` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_report_subscriber_daily_site_id_idx` (`site_id`),
  KEY `fk_report_subscriber_daily_service_id_idx` (`service_id`),
  CONSTRAINT `fk_report_subscriber_daily_service_id` FOREIGN KEY (`service_id`) REFERENCES `service` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_report_subscriber_daily_site_id` FOREIGN KEY (`site_id`) REFERENCES `site` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Bảng thống kê số lượng thuê bao';
SQL;
        $this->execute($sql);

    }

    public function down()
    {
        echo "m160428_033630_alter_table_report_subscriber_daily cannot be reverted.\n";

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
