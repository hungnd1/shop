<?php

use yii\db\Migration;

class m160617_080910_add_column_url_table_content_profile_site_asm extends Migration
{
    public function up()
    {
        $this->addColumn('content_profile_site_asm','url','varchar(1000) default null');
        $this->addColumn('content_profile_site_asm','sub_url','varchar(1000) default null');
    }

    public function down()
    {
        echo "m160617_080910_add_column_url_table_content_profile_site_asm cannot be reverted.\n";

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
