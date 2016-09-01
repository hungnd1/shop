<?php

use yii\db\Migration;

class m160414_100441_add_default_price_content_site extends Migration
{
    public function up()
    {
        $this->addColumn('site', 'default_price_content_id', 'int(11) default null');
    }

    public function down()
    {
        echo "m160414_100441_add_default_price_content_site cannot be reverted.\n";

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
