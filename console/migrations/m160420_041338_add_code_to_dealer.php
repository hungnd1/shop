<?php

use yii\db\Migration;

class m160420_041338_add_code_to_dealer extends Migration
{
    public function up()
    {
        $this->addColumn('dealer', 'code', $this->string(20)->notNull());
    }

    public function down()
    {
        $this->dropColumn('dealer', 'code');
    }
}
