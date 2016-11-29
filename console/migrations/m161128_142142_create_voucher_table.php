<?php

use yii\db\Migration;

/**
 * Handles the creation for table `voucher_table`.
 */
class m161128_142142_create_voucher_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('voucher', [
            'id' => $this->primaryKey(),
            'name' => $this->string(),
            'sale' => $this->double(),
            'status' => $this->integer(),
            'start_date' => $this->integer(),
            'end_date' => $this->integer(),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('voucher');
    }
}
