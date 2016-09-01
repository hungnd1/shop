<?php

use yii\db\Migration;

class m160726_072544_alter_streaming_server extends Migration
{
    public function up()
    {
        $this->dropColumn('streaming_server', 'url_regex');
        $this->dropColumn('streaming_server', 'type');
        $this->dropColumn('streaming_server', 'percent');

        $this->alterColumn('streaming_server', 'name', 'varchar(200) not null');
        $this->addColumn('streaming_server', 'ip', 'varchar(64) not null');
        $this->addColumn('streaming_server', 'port', 'int');
        $this->addColumn('streaming_server', 'content_status_api', 'varchar(255)');
        $this->addColumn('streaming_server', 'content_api', 'varchar(255)');
        $this->addColumn('streaming_server', 'content_path', 'varchar(255)');
        $this->addColumn('streaming_server', 'status', 'smallint not null');
    }

    public function down()
    {
        echo "m160726_072544_alter_streaming_server cannot be reverted.\n";

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
