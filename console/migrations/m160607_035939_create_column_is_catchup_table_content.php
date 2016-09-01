<?php

use yii\db\Migration;

class m160607_035939_create_column_is_catchup_table_content extends Migration
{
    public function up()
    {
        $sql =<<<SQL
ALTER TABLE `content`
ADD COLUMN `is_catchup` TINYINT(1) NOT NULL DEFAULT 0 AFTER `is_free`,
ADD INDEX `idx_content_is_catchup` (`is_catchup` ASC)
SQL;
        $this->execute($sql);
    }

    public function down()
    {
        $this->dropTable('column_is_catchup_table_content');
    }
}
