<?php

use yii\db\Migration;

class m160325_040718_create_unique_device_id_to_device extends Migration
{
    public function up()
    {
        $this->createIndex('uk_device_id', 'device', 'device_id', true);
    }

    public function down()
    {
        echo "m160325_040718_create_unique_device_id_to_device cannot be reverted.\n";

        return false;
    }
}
