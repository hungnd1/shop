<?php

use yii\db\Migration;

class m160321_094807_add_tvod1_id_to_category extends Migration
{
    public function up()
    {
        $this->addColumn('category', 'tvod1_id', $this->integer());
    }

    public function down()
    {
        $this->dropColumn('category', 'tvod1_id');
    }
}
