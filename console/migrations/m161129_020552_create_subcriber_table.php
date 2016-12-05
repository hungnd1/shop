<?php

use yii\db\Migration;

/**
 * Handles the creation for table `subcriber_table`.
 */
class m161129_020552_create_subcriber_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('subcriber', [
            'id' => $this->primaryKey(),
            'user_name' => $this->string(),
            'full_name' => $this->string(),
            'gender' => $this->integer(3),
            'status' => $this->integer(3),
            'auth_key' => $this->string(32)->notNull(),
            'password_hash' => $this->string()->notNull(),
            'password_reset_token' => $this->string()->unique(),
            'email' => $this->string()->notNull()->unique(),
            'address' => $this->string(),
            'phone' => $this->integer(),
            'birthday' => $this->dateTime(),
            'about' => $this->text(),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('subcriber_table');
    }
}
