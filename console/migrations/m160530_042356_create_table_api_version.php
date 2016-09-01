<?php

use yii\db\Migration;

class m160530_042356_create_table_api_version extends Migration
{
    public function up()
    {
        $sql =<<<SQL
CREATE TABLE `api_version` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NULL,
  `version` INT(11) NOT NULL,
  `type` INT(11) NOT NULL COMMENT '1:video, 2:live, 3:music, 4:news, 5:clips, 6:karaoke, 7: radio',
  `description` VARCHAR(255) NULL,
  `created_at` INT(11) NULL,
  `updated_at` INT(11) NULL,
  PRIMARY KEY (`id`))
SQL;
        $this->execute($sql);
    }

    public function down()
    {
        $this->dropTable('table_api_version');
    }
}
