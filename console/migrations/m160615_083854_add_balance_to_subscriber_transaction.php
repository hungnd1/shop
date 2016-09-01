<?php

use yii\db\Migration;

class m160615_083854_add_balance_to_subscriber_transaction extends Migration
{
    public function up()
    {
        $this->addColumn('subscriber_transaction', 'balance', $this->double(10));
    }

    public function down()
    {
        $this->dropColumn('subscriber_transaction', 'balance');
    }
}
