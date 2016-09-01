<?php

use yii\db\Migration;

class m160411_062026_add_expired_at_to_subscriber extends Migration
{
    public function up()
    {
        $this->addColumn('subscriber', 'expired_at', $this->integer());
    }

    public function down()
    {
        $this->dropColumn('subscriber', 'expired_at');
    }
}
