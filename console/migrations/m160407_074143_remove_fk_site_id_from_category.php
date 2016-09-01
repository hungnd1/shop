<?php

use yii\db\Schema;
use yii\db\Migration;

class m160407_074143_remove_fk_site_id_from_category extends Migration
{
    public function up()
    {
        $sql =<<<SQL
ALTER TABLE category DROP FOREIGN KEY fk_category_service_provider1;
SQL;
        $this->execute($sql);
    }

    public function down()
    {
        echo "m160407_074143_remove_fk_site_id_from_category cannot be reverted.\n";

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
