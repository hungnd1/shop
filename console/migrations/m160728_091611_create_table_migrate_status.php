    <?php

use yii\db\Migration;

/**
 * Handles the creation for table `table_migrate_status`.
 */
class m160728_091611_create_table_migrate_status extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $sql =<<<SQL
CREATE TABLE `migrate_status` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(50) NOT NULL,
  `max_id` int(11) NOT NULL,
  `last_migrated_at` int(11) DEFAULT NULL,
  `last_data_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB
SQL;
        $this->execute($sql);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('table_migrate_status');
    }
}
