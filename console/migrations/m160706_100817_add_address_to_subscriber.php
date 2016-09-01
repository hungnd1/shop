<?php

use yii\db\Migration;

/**
 * Handles adding address to table `subscriber`.
 */
class m160706_100817_add_address_to_subscriber extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->addColumn('subscriber', 'address', $this->string());
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropColumn('subscriber', 'address');
    }
}
