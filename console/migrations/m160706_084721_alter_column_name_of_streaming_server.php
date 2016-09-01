<?php

use yii\db\Migration;

class m160706_084721_alter_column_name_of_streaming_server extends Migration
{
    public function up()
    {
        $this->alterColumn('streaming_server', 'name', 'varchar(200) not null');
    }

    public function down()
    {
        echo "m160706_084721_alter_column_name_of_streaming_server cannot be reverted.\n";

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
