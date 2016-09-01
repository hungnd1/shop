<?php

use yii\db\Migration;

class m160401_102041_add_auto_increment_content_view_log extends Migration
{
    public function up()
    {
        $autoIncrementType = 'INT NOT NULL AUTO_INCREMENT';
        Yii::$app->db->createCommand()->checkIntegrity(false)->execute();
        $this->alterColumn('content_view_log', 'id', $autoIncrementType);
        Yii::$app->db->createCommand()->checkIntegrity(true)->execute();
        return true;
    }

    public function down()
    {
        echo "m160401_102041_add_auto_increment_content_view_log cannot be reverted.\n";

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
