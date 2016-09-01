<?php

use yii\db\Migration;

class m160429_083326_alter_table_report_subscriber_activity extends Migration
{
    public function up()
    {
        $this->renameColumn('report_subscriber_activity','via_site','via_site_daily');
        $this->renameColumn('report_subscriber_activity','view_content','total_via_site');
    }

    public function down()
    {
        echo "m160429_083326_alter_table_report_subscriber_activity cannot be reverted.\n";

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
