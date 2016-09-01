<?php

use yii\db\Migration;

class m160530_041202_create_table_actor_director extends Migration
{
    public function up()
    {
        $sql =<<<SQL
CREATE TABLE `actor_director` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NULL,
  `description` VARCHAR(200) NULL,
  `type` INT(11) NULL COMMENT '1:actor, 2: director',
  `content_type` INT(11) NULL COMMENT '1:video, 2:live, 3:music, 4:news, 5:clips, 6:karaoke, 7: radio',
  `image` VARCHAR(500) NULL,
  `status` INT(11) NULL COMMENT '10:active, 0:inactive',
  `created_at` INT(11) NULL,
  `updated_at` INT(11) NULL,
  PRIMARY KEY (`id`))
SQL;
        $this->execute($sql);
    }

    public function down()
    {
        $this->dropTable('table_actor_director');
    }
}
