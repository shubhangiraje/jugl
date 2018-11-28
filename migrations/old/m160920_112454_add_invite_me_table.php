<?php

use yii\db\Schema;
use yii\db\Migration;

class m160920_112454_add_invite_me_table extends Migration
{
    public function up()
    {
        $this->execute("
CREATE TABLE `invite_me` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `dt` timestamp NOT NULL DEFAULT '2000-01-01 00:00:00',
  `ip` varchar(20) NOT NULL,
  `first_name` varchar(64) NOT NULL,
  `last_name` varchar(64) NOT NULL,
  `email` varchar(128) NOT NULL,
  `phone` varchar(64) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        ");
    }

    public function down()
    {
        echo "m160920_112454_add_invite_me_table cannot be reverted.\n";

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
