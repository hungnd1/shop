<?php

use yii\db\Migration;

class m160412_090738_create_table_access_system extends Migration
{
    public function up()
    {
        $sql =<<<SQL
CREATE TABLE `access_system` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `subscriber_id` INT(11) NULL,
  `ip_address` VARCHAR(45) NULL,
  `user_agent` VARCHAR(255) NULL,
  `site_id` INT(11) NULL,
  `access_date` INT(11) NOT NULL,
  `created_at` INT(11) NULL,
  `updated_at` INT(11) NULL,
  `action` VARCHAR(126) NULL,
  `request_detail` VARCHAR(255) NULL,
  `request_params` TEXT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_access_system_access_date_idx` (`access_date` ASC),
  INDEX `fk_access_system_subscriber_id_idx` (`subscriber_id` ASC),
  INDEX `fk_access_system_site_id_idx` (`site_id` ASC),
  CONSTRAINT `fk_access_system_subscriber_id`
    FOREIGN KEY (`subscriber_id`)
    REFERENCES `subscriber` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_access_system_site_id`
    FOREIGN KEY (`site_id`)
    REFERENCES `site` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
COMMENT = 'Thống kê lượt truy cập vào hệ thống'
SQL;
        $this->execute($sql);
    }

    public function down()
    {
        $this->dropTable('table_access_system');
    }
}
