<?php

use yii\db\Migration;

class m160414_065407_add_curency_site extends Migration
{
    public function up()
    {
        $this->addColumn('site', 'currency', 'varchar(4) default "VND"');
    }

    public function down()
    {
        echo "m160414_065407_add_curency_site cannot be reverted.\n";

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
