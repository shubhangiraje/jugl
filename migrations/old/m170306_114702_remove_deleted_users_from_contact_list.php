<?php

use yii\db\Migration;

class m170306_114702_remove_deleted_users_from_contact_list extends Migration
{
    public function up()
    {
        $this->execute("
delete from chat_conversation where second_user_id in (select id from user where status='DELETED');
        ");

        $this->execute("
delete from chat_user_contact where  second_user_id in (select id from user where status='DELETED');
        ");
    }

    public function down()
    {
        echo "m170306_114702_remove_deleted_users_from_contact_list cannot be reverted.\n";

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
