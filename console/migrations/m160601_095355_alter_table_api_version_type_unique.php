<?php

use yii\db\Migration;

class m160601_095355_alter_table_api_version_type_unique extends Migration
{
    public function up()
    {
        $sql =<<<SQL
ALTER TABLE `api_version`
ADD COLUMN `site_id` INT(11)  NOT NULL AFTER `description`,
ADD UNIQUE INDEX `idx_api_version_type_site_id` (`type` ASC, `site_id` ASC),
ADD INDEX `idx_api_version_site_id` (`site_id` ASC);
ALTER TABLE `api_version`
ADD CONSTRAINT `fk_api_version_site_id`
  FOREIGN KEY (`site_id`)
  REFERENCES `site` (`id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION;
SQL;
        $this->execute($sql);
    }

    public function down()
    {
        echo "m160601_095355_alter_table_api_version_type_unique cannot be reverted.\n";

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
