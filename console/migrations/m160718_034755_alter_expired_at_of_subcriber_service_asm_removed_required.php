<?php

use yii\db\Migration;

class m160718_034755_alter_expired_at_of_subcriber_service_asm_removed_required extends Migration
{
    public function up()
    {
        $this->alterColumn('subscriber_service_asm', 'expired_at', 'int');
    }

    public function down()
    {
        echo "m160718_034755_alter_expired_at_of_subcriber_service_asm_removed_required cannot be reverted.\n";

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
