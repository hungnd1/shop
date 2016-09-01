<?php

use yii\db\Migration;

class m160323_070539_add_tvod1_id_to_content extends Migration
{
    public function up()
    {
        $this->addColumn('content', 'tvod1_id', $this->integer());
    }

    public function down()
    {
        $this->dropColumn('content', 'tvod1_id');
    }
}
