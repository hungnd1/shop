<?php

use yii\db\Migration;

/**
 * Handles adding desc to table `migrate_status`.
 */
class m160810_082136_add_desc_to_migrate_status extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->addColumn('migrate_status', 'desc', $this->string());
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropColumn('migrate_status', 'desc');
    }
}
