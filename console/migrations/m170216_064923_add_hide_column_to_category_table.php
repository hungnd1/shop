<?php

use yii\db\Migration;

/**
 * Handles adding hide to table `category`.
 */
class m170216_064923_add_hide_column_to_category_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->addColumn('category', 'hide', $this->integer());
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropColumn('category', 'hide');
    }
}
