<?php

use yii\db\Migration;

class m160620_022453_delete_column_sub_url_table_content_profile_site_asm extends Migration
{
    public function up()
    {
        $this->dropColumn('content_profile_site_asm','sub_url');
    }

    public function down()
    {
        echo "m160620_022453_delete_column_sub_url_table_content_profile_site_asm cannot be reverted.\n";

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
