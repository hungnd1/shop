<?php

use yii\db\Migration;
use yii\db\mysql\Schema;

class m160422_091304_add_table_file_transcoded extends Migration
{
    public function up()
    {
        $this->createTable('file_transcoded', [
            'id' => $this->primaryKey(11),
            'title' => $this->string(255)->defaultValue(null),
            'basedir' => $this->string(255)->defaultValue(null),
            'type' => $this->integer(2)->defaultValue(1),
            'cdn_id' => $this->integer(11)->notNull(),
            'picture' => $this->string(255),
            'duration' => $this->integer(11),
            'resolution' => $this->string(255),
            'created_at' => $this->integer(11),
            'updated_at' => $this->integer(11),
        ]);
    }

    public function down()
    {
        echo "m160422_091304_add_table_file_transcoded cannot be reverted.\n";

        return false;
    }

    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
