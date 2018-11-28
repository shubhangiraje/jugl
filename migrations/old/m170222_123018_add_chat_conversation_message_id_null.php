<?php

use yii\db\Migration;

class m170222_123018_add_chat_conversation_message_id_null extends Migration
{
    public function up()
    {
        $this->execute("
ALTER TABLE `chat_conversation`
CHANGE `last_chat_message_id` `last_chat_message_id` bigint(20) NULL AFTER `second_user_id`;
        ");
    }

    public function down()
    {
        echo "m170222_123018_add_chat_conversation_message_id_null cannot be reverted.\n";

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
