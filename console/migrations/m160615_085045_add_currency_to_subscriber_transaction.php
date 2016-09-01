<?php

use yii\db\Migration;

class m160615_085045_add_currency_to_subscriber_transaction extends Migration
{
    public function up()
    {
        $this->addColumn('subscriber_transaction', 'currency', $this->string(4));
    }

    public function down()
    {
        $this->dropColumn('subscriber_transaction', 'currency');
    }
}
