<?php

use yii\db\Migration;

class m160412_074747_create_table_report_user_daily extends Migration
{
    public function up()
    {
        $sql =<<<SQL
CREATE TABLE `report_user_daily` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `report_date` int(11) DEFAULT NULL,
  `site_id` int(11) NOT NULL,
  `active_user` int(11) DEFAULT '0',
  `active_user_package` int(11) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `fk_report_user_daily_site_id_idx` (`site_id`),
  CONSTRAINT `fk_report_user_daily_site_id` FOREIGN KEY (`site_id`) REFERENCES `site` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB
SQL;
        $this->execute($sql);
    }

    public function down()
    {
        $this->dropTable('table_report_user_daily');
    }
}
