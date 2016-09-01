<?php

use yii\db\Migration;

/**
 * Handles adding en_name to table `content`.
 */
class m160728_101727_add_en_name_to_content extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->addColumn('content', 'en_name', 'varchar(255)');
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropColumn('content', 'en_name');
    }
}
