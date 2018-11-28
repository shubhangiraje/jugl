<?php

use yii\db\Schema;
use yii\db\Migration;

class m150325_082155_add_remote_log_table extends Migration
{
    public function up()
    {
        $this->execute("
            CREATE TABLE `remote_log` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `session` varchar(256) NOT NULL,
              `dt` varchar(32) NOT NULL,
              `type` enum('LOG','DEBUG','INFO','WARN','ERROR') NOT NULL,
              `message` mediumtext NOT NULL,
              PRIMARY KEY (`id`),
              KEY `session_dt` (`session`(255),`dt`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        ");
    }

    public function down()
    {
        echo "m150325_082155_add_remote_log_table cannot be reverted.\n";

        return false;
    }
}
