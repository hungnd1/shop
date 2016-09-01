<?php

use yii\db\Migration;

class m160411_012449_add_expired_month_to_device extends Migration
{
    public function up()
    {
        $this->addColumn('device', 'expired_month', $this->integer());
    }

    public function down()
    {
        $this->dropColumn('device', 'expired_month');
    }
}
