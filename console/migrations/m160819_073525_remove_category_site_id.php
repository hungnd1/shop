<?php

use yii\db\Migration;

class m160819_073525_remove_category_site_id extends Migration
{
    public function up()
    {
        $this->dropColumn('category', 'site_id');
    }

    public function down()
    {
        echo "m160819_073525_remove_category_site_id cannot be reverted.\n";

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
