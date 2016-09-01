<?php

use yii\db\Migration;

class m160818_071145_create_column_default_category_id_table_content extends Migration
{
    public function up()
    {
        $sql =<<<SQL
ALTER TABLE `content`
ADD COLUMN `default_category_id` INT(11) NULL AFTER `default_site_id`,
ADD INDEX `idx_content_default_category_id` (`default_category_id` ASC);
SQL;
        $this->execute($sql);
    }

    public function down()
    {
        $this->dropTable('column_default_category_id_table_content');
    }
}
