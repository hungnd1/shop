<?php

use yii\db\Migration;

class m160420_074651_add_full_type_table_service extends Migration
{
    public function up()
    {
        $this->addColumn('service','full_types','varchar(256) default null');
    }

    public function down()
    {
        echo "m160420_074651_add_full_type_table_service cannot be reverted.\n";

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
