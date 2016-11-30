<?php

use yii\db\Migration;

class m161129_071401_alter_table_content extends Migration
{
    public function up()
    {
        $this->addColumn('content','availability','int(11)');
        $this->addColumn('content','type_status','int(11)');
    }

    public function down()
    {
        echo "m161129_071401_alter_table_content cannot be reverted.\n";

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
