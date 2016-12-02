<?php

use yii\db\Migration;

class m161130_130631_add_column_type_table_category extends Migration
{
    public function up()
    {
        $this->addColumn('category','type','int(11)');
    }

    public function down()
    {
        echo "m161130_130631_add_column_type_table_category cannot be reverted.\n";

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
