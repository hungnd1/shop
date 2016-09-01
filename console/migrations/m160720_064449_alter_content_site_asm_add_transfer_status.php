<?php

use yii\db\Migration;

class m160720_064449_alter_content_site_asm_add_transfer_status extends Migration
{
    public function up()
    {
        $this->addColumn('content_site_asm', 'transfer_status', $this->integer());
    }

    public function down()
    {
        $this->dropColumn('content_site_asm', 'transfer_status');
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
