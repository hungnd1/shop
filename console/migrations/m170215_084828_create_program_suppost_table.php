<?php

use yii\db\Migration;

/**
 * Handles the creation of table `program_suppost`.
 */
class m170215_084828_create_program_suppost_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('program_suppost', [
            'id' => $this->primaryKey(),
            'image' => $this->string(),
            'name' => $this->string(),
            'short_des' => $this->string(),
            'des' => $this->text(),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
            'status' => $this->integer(),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('program_suppost');
    }
}
