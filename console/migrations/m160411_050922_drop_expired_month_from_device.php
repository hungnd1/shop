<?php

use yii\db\Migration;

class m160411_050922_drop_expired_month_from_device extends Migration
{
    public function up()
    {
        $this->dropColumn('device', 'expired_month');
    }

    public function down()
    {
        $this->addColumn('device', 'expired_month', $this->integer());
    }
}
