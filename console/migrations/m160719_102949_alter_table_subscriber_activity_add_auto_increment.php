<?php

use yii\db\Migration;

class m160719_102949_alter_table_subscriber_activity_add_auto_increment extends Migration
{
    public function up()
    {
//        $sql = <<<SQL
//ALTER TABLE `subscriber_activity`
//CHANGE COLUMN `id` `id` BIGINT(20) NOT NULL AUTO_INCREMENT ;
//
//SQL;
//        $this->execute($sql);
        $autoIncrementType = 'BIGINT(20) NOT NULL AUTO_INCREMENT';
        Yii::$app->db->createCommand()->checkIntegrity(false)->execute();
        $this->alterColumn('subscriber_activity', 'id', $autoIncrementType);
        Yii::$app->db->createCommand()->checkIntegrity(true)->execute();
    }

    public function down()
    {
        echo "m160719_102949_alter_table_subscriber_activity_add_auto_increment cannot be reverted.\n";

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
