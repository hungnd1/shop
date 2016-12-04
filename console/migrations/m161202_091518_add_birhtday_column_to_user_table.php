<?php

use yii\db\Migration;

/**
 * Handles adding birhtday to table `user`.
 */
class m161202_091518_add_birhtday_column_to_user_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->addColumn('user', 'birthday', $this->dateTime());
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropColumn('user', 'birthday');
    }
}
