<?php

use yii\db\Migration;

class m160603_090707_add_identification_and_issued_by_to_subscriber extends Migration
{
    public function up()
    {
        $this->addColumn('subscriber', 'identification', $this->string());
        $this->addColumn('subscriber', 'issued_by', $this->string());
    }

    public function down()
    {
        $this->dropColumn('subscriber', 'identification');
        $this->dropColumn('subscriber', 'issued_by');
    }
}
