<?php

use yii\db\Migration;

class m160329_033554_add_dealer_id_to_device extends Migration
{
    public function up()
    {
        $this->addColumn('device', 'dealer_id', 'INT(11) DEFAULT NULL AFTER `site_id`');
        $this->createIndex('fk_device_dealer_idx','device','dealer_id');
        $this->addForeignKey('fk_device_dealer','device','dealer_id','dealer','id');
    }

    public function down()
    {
        $this->dropColumn('device', 'dealer_id');
    }
}
