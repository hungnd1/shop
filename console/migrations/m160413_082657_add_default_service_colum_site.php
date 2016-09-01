<?php

use yii\db\Migration;

class m160413_082657_add_default_service_colum_site extends Migration
{
    public function up()
    {
        $this->addColumn('site', 'default_service_id', 'int(11) default null');
    }

    public function down()
    {
        echo "m160413_082657_add_default_service_colum_site cannot be reverted.\n";

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
