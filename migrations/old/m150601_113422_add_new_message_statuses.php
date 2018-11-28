<?php

use yii\db\Schema;
use yii\db\Migration;

class m150601_113422_add_new_message_statuses extends Migration
{
    public function up()
    {
        $this->execute("
            ALTER TABLE `chat_message`
            CHANGE `type` `type` enum('OUTGOING_UNDELIVERED','OUTGOING_UNREADED','OUTGOING_READED','INCOMING_UNDELIVERED','INCOMING_UNREADED','INCOMING_READED') COLLATE 'utf8_general_ci' NOT NULL AFTER `outgoing_chat_message_id`,
            COMMENT='';
        ");
    }

    public function down()
    {
        echo "m150601_113422_add_new_message_statuses cannot be reverted.\n";

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
