<?php

use yii\db\Migration;

class m161206_153524_add_column_type extends Migration
{
    public function up()
    {
        $this->addColumn('slide','type','int(11)');
    }

    public function down()
    {
        echo "m161206_153524_add_column_type cannot be reverted.\n";

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
