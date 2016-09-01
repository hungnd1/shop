<?php

use yii\db\Migration;

class m160426_102351_add_origin_url_table_content extends Migration
{
    public function up()
    {
        $this->addColumn('content', 'origin_url', 'varchar(255) default null');
    }

    public function down()
    {
        echo "m160426_102351_add_origin_url_table_content cannot be reverted.\n";

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
