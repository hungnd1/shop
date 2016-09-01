<?php

use yii\db\Migration;

/**
 * Handles the dropping for table `content_site_status_asm`.
 */
class m160620_050341_drop_content_site_status_asm extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->dropTable('content_site_status_asm');
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->createTable('content_site_status_asm', [
            'id' => $this->primaryKey(),
        ]);
    }
}
