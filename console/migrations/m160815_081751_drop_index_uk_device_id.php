<?php

use yii\db\Migration;

/**
 * Handles the dropping for table `index_uk_device_id`.
 */
class m160815_081751_drop_index_uk_device_id extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->dropIndex('uk_device_id', 'device');
        $this->createIndex('idx_device_id', 'device', 'device_id');
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->createTable('index_uk_device_id', [
            'id' => $this->primaryKey(),
        ]);
    }
}
