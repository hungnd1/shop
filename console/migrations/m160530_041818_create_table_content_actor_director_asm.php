<?php

use yii\db\Migration;

class m160530_041818_create_table_content_actor_director_asm extends Migration
{
    public function up()
    {
        $sql =<<<SQL
CREATE TABLE `content_actor_director_asm` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `content_id` INT(11) NULL,
  `actor_director_id` INT(11) NULL,
  `description` VARCHAR(255) NULL,
  `created_at` INT(11) NULL,
  PRIMARY KEY (`id`),
  INDEX `idx_content_actor_director_asm_content_id` (`content_id` ASC),
  INDEX `idx_content_actor_director_asm_actor_director_id` (`actor_director_id` ASC),
  CONSTRAINT `fk_content_actor_director_asm_content_id`
    FOREIGN KEY (`content_id`)
    REFERENCES `content` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_content_actor_director_asm_actor_director_id`
    FOREIGN KEY (`actor_director_id`)
    REFERENCES `actor_director` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION);

SQL;
        $this->execute($sql);
    }

    public function down()
    {
        $this->dropTable('table_content_actor_director_asm');
    }
}
