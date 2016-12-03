<?php

use yii\db\Migration;

/**
 * Handles adding gender_about to table `user`.
 */
class m161203_005529_add_gender_about_column_to_user_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->addColumn('user', 'gender', $this->integer(3));
        $this->addColumn('user', 'about', $this->string(500));
        $this->addColumn('user', 'image', $this->string(500));
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropColumn('user', 'gender');
        $this->dropColumn('user', 'about');
        $this->dropColumn('user', 'image');
    }
}
