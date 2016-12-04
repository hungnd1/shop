<?php

use yii\db\Migration;

/**
 * Handles adding address to table `user`.
 */
class m161202_084802_add_address_column_to_user_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->addColumn('user', 'address', $this->string(500));
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropColumn('user', 'address');
    }
}
