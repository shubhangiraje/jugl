<?php

use yii\db\Schema;
use yii\db\Migration;

class m151027_133328_add_invitations_to_user extends Migration
{
    public function up()
    {
        $this->execute("
            ALTER TABLE `user`
            ADD `invitations` int(11) NOT NULL DEFAULT '0' AFTER `network_size`,
            COMMENT='';
        ");

        $this->execute("
            update user
            join (select user_id,count(*) as cnt from invitation group by user_id) as mytab on (mytab.user_id=user.id)
            set invitations=mytab.cnt
        ");
    }

    public function down()
    {
        echo "m151027_133328_add_invitations_to_user cannot be reverted.\n";

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
