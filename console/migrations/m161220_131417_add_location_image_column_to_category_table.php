<?php

use yii\db\Migration;

/**
 * Handles adding location_image to table `category`.
 */
class m161220_131417_add_location_image_column_to_category_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->addColumn('category', 'location_image', $this->integer());
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropColumn('category', 'location_image');
    }
}
