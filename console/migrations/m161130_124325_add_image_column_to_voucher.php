<?php

use yii\db\Migration;

/**
 * Handles adding image_column to table `voucher`.
 */
class m161130_124325_add_image_column_to_voucher extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->addColumn('voucher', 'image', $this->string());
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropColumn('voucher', 'image');
    }
}
