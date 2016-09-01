<?php

use yii\db\Migration;

class m160712_040351_create_column_city_table_subscriber extends Migration
{
    public function up()
    {
        $sql = <<<SQL
ALTER TABLE `subscriber`
ADD COLUMN `city` VARCHAR(255) NULL AFTER `address`
SQL;
        $this->execute($sql);
    }

    public function down()
    {
        $this->dropTable('column_city_table_subscriber');
    }
}
