<?php

use yii\db\Schema;
use yii\db\Migration;

class m160413_114602_update_user_invitations extends Migration
{
    public function up()
    {
        $this->execute("
            update user u
            left outer join (select user_id,count(*) as cnt from invitation where referral_user_id is not null group by user_id) as t on (t.user_id=u.id)
            set u.invitations=coalesce(t.cnt,0)
        ");
    }

    public function down()
    {
        echo "m160413_114602_update_user_invitations cannot be reverted.\n";

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
