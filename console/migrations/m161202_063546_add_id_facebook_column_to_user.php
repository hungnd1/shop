<?php

use yii\db\Migration;

class m161202_063546_add_id_facebook_column_to_user extends Migration
{
    public function up()
    {
        $this->addColumn('user', 'id_facebook', $this->string());
    }

    public function down()
    {
        $this->dropColumn('user', 'id_facebook');
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
