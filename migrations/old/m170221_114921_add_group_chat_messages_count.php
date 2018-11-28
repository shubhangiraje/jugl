<?php

use yii\db\Migration;

class m170221_114921_add_group_chat_messages_count extends Migration
{
    public function up()
    {
        $this->execute("
            ALTER TABLE `chat_user`
            ADD `group_chat_messages_count` int NOT NULL DEFAULT '0' AFTER `group_chat_title`;            
        ");
    }

    public function down()
    {
        echo "m170221_114921_add_group_chat_messages_count cannot be reverted.\n";

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
