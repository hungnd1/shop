<?php

use yii\db\Migration;

/**
 * Handles the creation for table `kodi_category`.
 */
class m160802_030255_create_kodi_category extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $sql =<<<SQL
CREATE TABLE `kodi_category` (
  `id` INT NOT NULL AUTO_INCREMENT COMMENT '',
  `description` TEXT NULL COMMENT '',
  `display_name` VARCHAR(50) NOT NULL COMMENT '',
  `image` VARCHAR(255) NOT NULL COMMENT '',
  `status` INT NULL COMMENT '',
  `created_at` INT COMMENT '',
  `updated_at` INT COMMENT '',
  PRIMARY KEY (`id`)  COMMENT '')
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_bin
COMMENT = 'danh muc cua kodi	'


SQL;
        $this->execute($sql);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('kodi_category');
    }
}
