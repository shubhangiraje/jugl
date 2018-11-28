<?php

use yii\db\Schema;
use yii\db\Migration;

class m151027_070744_add_system_message_type extends Migration
{
    public function up()
    {
        $this->execute("
            ALTER TABLE `chat_message`
            CHANGE `type` `type` enum('OUTGOING_UNDELIVERED','OUTGOING_UNREADED','OUTGOING_READED','INCOMING_UNDELIVERED','INCOMING_UNREADED','INCOMING_READED','SYSTEM') COLLATE 'utf8_general_ci' NOT NULL AFTER `outgoing_chat_message_id`,
            COMMENT='';
        ");
    }

    public function down()
    {
        echo "m151027_070744_add_system_message_type cannot be reverted.\n";

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
