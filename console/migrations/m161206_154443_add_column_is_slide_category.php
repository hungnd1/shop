<?php

use yii\db\Migration;

class m161206_154443_add_column_is_slide_category extends Migration
{
    public function up()
    {
        $this->addColumn('content','is_slide_category','int(11)');
    }

    public function down()
    {
        echo "m161206_154443_add_column_is_slide_category cannot be reverted.\n";

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
