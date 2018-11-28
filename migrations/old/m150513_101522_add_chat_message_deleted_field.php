<?php

use yii\db\Schema;
use yii\db\Migration;

class m150513_101522_add_chat_message_deleted_field extends Migration
{
    public function up()
    {

    }

    public function down()
    {
        echo "m150513_101522_add_chat_message_deleted_field cannot be reverted.\n";

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
