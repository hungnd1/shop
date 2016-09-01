<?php

use yii\db\Migration;

/**
 * Handles the dropping for table `url_sub_url_content_profile`.
 */
class m160620_031040_drop_url_sub_url_content_profile extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->dropColumn('content_profile', 'url');
        $this->dropColumn('content_profile', 'sub_url');
    }

    /**
     * @inheritdoc
     */
    public function down()
    {

    }
}
