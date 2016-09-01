<?php

use yii\db\Migration;

class m160329_034106_add_dealer_id_to_subscriber extends Migration
{
    public function up()
    {
        $this->addColumn('subscriber', 'dealer_id', 'INT(11) DEFAULT NULL AFTER `site_id`');
        $this->createIndex('fk_subscriber_dealer_idx','subscriber','dealer_id');
        $this->addForeignKey('fk_subscriber_dealer','subscriber','dealer_id','dealer','id');
    }

    public function down()
    {
        $this->dropColumn('subscriber', 'dealer_id');
    }
}
