<?php

use common\models\Device;
use yii\db\Migration;

class m160325_041201_add_status_to_device extends Migration
{
    public function up()
    {
        $this->addColumn('device', 'status', $this->integer()->notNull()->defaultValue(Device::STATUS_ACTIVE));
    }

    public function down()
    {
        $this->dropColumn('device', 'status');
    }
}
