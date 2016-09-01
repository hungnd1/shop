<?php

use yii\db\Migration;

class m160610_040421_drop_identification_and_issued_by_from_subscriber extends Migration
{
    public function up()
    {
        $this->dropColumn('subscriber', 'identification');
        $this->dropColumn('subscriber', 'issued_by');
    }

    public function down()
    {
        $this->addColumn('subscriber', 'identification', $this->string());
        $this->addColumn('subscriber', 'issued_by', $this->string());
    }
}
