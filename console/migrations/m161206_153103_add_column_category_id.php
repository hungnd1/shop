<?php

use yii\db\Migration;

class m161206_153103_add_column_category_id extends Migration
{
    public function up()
    {
        $this->addColumn('slide','category_id','int(11)');
    }

    public function down()
    {
        echo "m161206_153103_add_column_category_id cannot be reverted.\n";

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
