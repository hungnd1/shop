<?php

use yii\db\Migration;

class m160411_012547_add_expired_at_to_device extends Migration
{
    public function up()
    {
        $this->addColumn('device', 'expired_at', $this->integer());
    }

    public function down()
    {
        $this->dropColumn('device', 'expired_at');
    }
}
