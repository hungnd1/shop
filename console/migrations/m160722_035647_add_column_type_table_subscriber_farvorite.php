<?php

use yii\db\Migration;

class m160722_035647_add_column_type_table_subscriber_farvorite extends Migration
{
    public function up()
    {
$sql = <<<SQL
ALTER TABLE `subscriber_favorite`
ADD COLUMN `type` SMALLINT(6) NOT NULL DEFAULT 1 COMMENT '1: video, 2: live, 3: music, 4:news, 5: clips, 6:karaoke, 7:radio, 8: live_content' AFTER `site_id`;
SQL;
        $this->execute($sql);
    }

    public function down()
    {
        echo "m160722_035647_add_column_type_table_subscriber_farvorite cannot be reverted.\n";

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
