<?php

use yii\db\Migration;

class m160420_003206_add_activated_at_to_device extends Migration
{
    public function up()
    {
        $this->addColumn('device', 'activated_at', $this->integer());
    }

    public function down()
    {
        $this->dropColumn('device', 'activated_at');
    }
}
