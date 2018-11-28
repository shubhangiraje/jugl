<?php

use yii\db\Schema;
use yii\db\Migration;

class m151005_093418_add_chat_message_extra_field extends Migration
{
    public function up()
    {
        $this->execute("
ALTER TABLE `chat_message`
DROP `geopos_lattitude`,
CHANGE `geopos_longitude` `extra` varchar(4096) COLLATE 'utf8_general_ci' NULL AFTER `text`,
COMMENT='';
        ");
    }

    public function down()
    {
        echo "m151005_093418_add_chat_message_extra_field cannot be reverted.\n";

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
