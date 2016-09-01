<?php

use yii\db\Migration;

/**
 * Handles the creation for table `subtitle_content_site_asm`.
 */
class m160620_045701_create_subtitle_content_site_asm extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->addColumn('content_site_asm', 'subtitle', $this->string());
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropColumn('content_site_asm', 'subtitle');
    }
}
