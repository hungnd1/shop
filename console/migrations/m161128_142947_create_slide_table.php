<?php

use yii\db\Migration;

/**
 * Handles the creation for table `slide_table`.
 */
class m161128_142947_create_slide_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('slide', [
            'id' => $this->primaryKey(),
            'id_content' => $this->integer(),
            'status' => $this->integer(),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('slide');
    }
}
