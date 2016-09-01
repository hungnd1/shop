<?php

use yii\db\Migration;

class m160325_041459_add_site_id_to_device extends Migration
{
    public function up()
    {
        $this->addColumn('device', 'site_id', $this->integer()->notNull());
        $this->addForeignKey('fk-device-site_id', 'device', 'site_id', 'site', 'id');
    }

    public function down()
    {
        $this->dropColumn('device', 'site_id');
    }
}
