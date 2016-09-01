<?php

use yii\db\Migration;

class m160418_101419_add_catchup_id_to_content extends Migration
{
    public function up()
    {
        $this->addColumn('content', 'catchup_id', $this->integer());
    }

    public function down()
    {
        $this->dropColumn('content', 'catchup_id');
    }
}
