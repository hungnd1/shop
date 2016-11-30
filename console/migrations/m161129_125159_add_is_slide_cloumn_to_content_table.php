<?php

use yii\db\Migration;

/**
 * Handles adding is_slide_cloumn to table `content_table`.
 */
class m161129_125159_add_is_slide_cloumn_to_content_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->addColumn('content', 'is_slide', $this->integer(3));
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropColumn('content', 'is_slide');
    }
}
