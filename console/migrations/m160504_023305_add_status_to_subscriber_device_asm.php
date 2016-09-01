<?php

use yii\db\Migration;

class m160504_023305_add_status_to_subscriber_device_asm extends Migration
{
    public function up()
    {
        $this->addColumn('subscriber_device_asm', 'status', $this->integer()->defaultValue(1)->notNull());
    }

    public function down()
    {
        $this->dropColumn('subscriber_device_asm', 'status');
    }
}
