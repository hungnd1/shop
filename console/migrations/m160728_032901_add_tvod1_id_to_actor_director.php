<?php

use yii\db\Migration;

/**
 * Handles adding tvod1_id to table `actor_director`.
 */
class m160728_032901_add_tvod1_id_to_actor_director extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->addColumn('actor_director', 'tvod1_id', $this->integer());
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropColumn('actor_director', 'tvod1_id');
    }
}
