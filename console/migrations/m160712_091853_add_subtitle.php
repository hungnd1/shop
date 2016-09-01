<?php

use yii\db\Migration;

class m160712_091853_add_subtitle extends Migration
{
    public function up()
    {
        $this->addColumn('content_profile', 'subtitle', $this->string());
    }

    public function down()
    {
        $this->dropColumn('content_profile', 'subtitle');
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
