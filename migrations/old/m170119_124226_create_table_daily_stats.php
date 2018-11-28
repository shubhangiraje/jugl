<?php

use yii\db\Migration;

class m170119_124226_create_table_daily_stats extends Migration
{
    public function up()
    {
        $this->execute("
            CREATE TABLE `daily_stats` (`dt` date NOT NULL, `packet_upgrades` int(11) NOT NULL DEFAULT '0', PRIMARY KEY (`dt`)) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        ");
    }

    public function down()
    {
        echo "m170119_124226_create_table_daily_stats cannot be reverted.\n";

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
