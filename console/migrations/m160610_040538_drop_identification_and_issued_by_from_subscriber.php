<?php

use yii\db\Migration;

class m160610_040538_drop_identification_and_issued_by_from_subscriber extends Migration
{
    public function up()
    {
        $this->dropColumn('subscriber', 'address');
    }

    public function down()
    {
        $this->addColumn('subscriber', 'address', $this->string());
    }
}
