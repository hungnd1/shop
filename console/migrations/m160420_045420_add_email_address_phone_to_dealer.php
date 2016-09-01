<?php

use yii\db\Migration;

class m160420_045420_add_email_address_phone_to_dealer extends Migration
{
    public function up()
    {
        $this->addColumn('dealer', 'email', $this->string(255));
        $this->addColumn('dealer', 'address', $this->string(255));
        $this->addColumn('dealer', 'phone', $this->string(45));
    }

    public function down()
    {
        $this->dropColumn('dealer', 'email');
        $this->dropColumn('dealer', 'address');
        $this->dropColumn('dealer', 'phone');
    }
}
