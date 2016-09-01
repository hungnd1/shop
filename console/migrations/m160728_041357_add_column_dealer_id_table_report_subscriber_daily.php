<?php

use yii\db\Migration;

class m160728_041357_add_column_dealer_id_table_report_subscriber_daily extends Migration
{
    public function up()
    {
        $sql = <<<SQL
ALTER TABLE `report_subscriber_daily`
ADD COLUMN `dealer_id` INT(11) NULL AFTER `site_id`,
ADD INDEX `idx_report_subscriber_daily_report_date` (`report_date` ASC),
ADD INDEX `idx_report_subscriber_daily_dealer_id` (`dealer_id` ASC);
SQL;
        $this->execute($sql);
    }

    public function down()
    {
        echo "m160728_041357_add_column_dealer_id_table_report_subscriber_daily cannot be reverted.\n";

        return false;
    }

    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
