<?php

use yii\db\Migration;

class m160823_100501_add_category_is_series extends Migration
{
    public function up()
    {
        $this->addColumn('category', 'is_series', 'int(11) default 0');
    }

    public function down()
    {
        $this->dropColumn('category', 'is_series');
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
