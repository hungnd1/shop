<?php

use yii\db\Migration;

/**
 * Handles adding is_news to table `category`.
 */
class m161230_091931_add_is_news_column_to_category_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->addColumn('category', 'is_news', $this->integer());
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropColumn('category', 'is_news');
    }
}
