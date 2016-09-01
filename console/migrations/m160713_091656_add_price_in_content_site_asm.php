<?php

use yii\db\Migration;

class m160713_091656_add_price_in_content_site_asm extends Migration
{
    public function up()
    {
        $this->dropTable('price_content_site');
        $this->addColumn('content_site_asm', 'price_id', $this->integer());
    }

    public function down()
    {
        $this->dropColumn('content_site_asm', 'price_id');
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
