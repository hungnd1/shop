<?php

use yii\db\Migration;

class m160603_091055_add_address_to_subscriber extends Migration
{
    public function up()
    {
        $this->addColumn('subscriber', 'address', $this->string());
    }

    public function down()
    {
        $this->dropColumn('subscriber', 'address');
    }
}
