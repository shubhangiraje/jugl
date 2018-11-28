<?php

use yii\db\Migration;

class m170317_095217_add_user_become_member_invitation_is_winner extends Migration
{
    public function up()
    {
        $this->execute("
            ALTER TABLE `user_become_member_invitation`
            ADD `is_winner` tinyint(1) NOT NULL DEFAULT '0';
        ");

        $this->execute("
            update user_become_member_invitation ubmi
            join (select user_id,min(dt) as dt from user_become_member_invitation group by user_id) t on (ubmi.user_id=t.user_id and ubmi.dt=t.dt) 
            set is_winner=1
        ");
    }

    public function down()
    {
        echo "m170317_095217_add_user_become_member_invitation_is_winner cannot be reverted.\n";

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
