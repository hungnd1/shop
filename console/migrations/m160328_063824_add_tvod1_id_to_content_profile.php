<?php

use yii\db\Migration;

class m160328_063824_add_tvod1_id_to_content_profile extends Migration
{
    public function up()
    {
        $this->addColumn('content_profile', 'tvod1_id', $this->integer());
    }

    public function down()
    {
        $this->dropColumn('content_profile', 'tvod1_id');
    }
}
