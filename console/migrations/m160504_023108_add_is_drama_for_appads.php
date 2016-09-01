<?php

use yii\db\Schema;
use yii\db\Migration;

class m160504_023108_add_is_drama_for_appads extends Migration
{
    public function up()
    {
        $this->addColumn('app_ads', 'is_drama', 'integer');
    }

    public function down()
    {
        $this->dropColumn('app_ads', 'is_drama');
    }
}
