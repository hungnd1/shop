<?php

use yii\db\Migration;

class m160321_091228_add_auto_increment_category extends Migration
{
    public function up()
    {
        $autoIncrementType = 'INT NOT NULL AUTO_INCREMENT';
        Yii::$app->db->createCommand()->checkIntegrity(false)->execute();
        $this->alterColumn('category', 'id', $autoIncrementType);
        Yii::$app->db->createCommand()->checkIntegrity(true)->execute();
        return true;
    }

    public function down()
    {
        echo "m160321_091228_add_auto_increment_category cannot be reverted.\n";

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
