<?php

use yii\db\Migration;

class m160824_050950_add_content_live_order extends Migration
{
    public function up()
    {
        $this->addColumn('content', 'order', 'int(11) default 0');
    }

    public function down()
    {
        $this->dropColumn('content', 'order');
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
