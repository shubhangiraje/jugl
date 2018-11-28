<?php

use yii\db\Migration;

class m161216_121847_add_new_fields extends Migration
{
    public function up()
    {
        $this->execute("
            ALTER TABLE `user`
            ADD `registered_by_become_member` tinyint(1) NOT NULL DEFAULT '0' AFTER `show_in_become_member`;
        ");

        $this->execute("
            ALTER TABLE `user`
            ADD `stat_invitations_email` int NOT NULL DEFAULT '0' AFTER `stat_offers_view_buy_ratio`,
            ADD `stat_invitations_sms` int NOT NULL DEFAULT '0' AFTER `stat_invitations_email`,
            ADD `stat_invitations_whatsapp` int NOT NULL DEFAULT '0' AFTER `stat_invitations_sms`,
            ADD `stat_invitations_social` int NOT NULL DEFAULT '0' AFTER `stat_invitations_whatsapp`,
            ADD `stat_referrals_standart` int NOT NULL DEFAULT '0' AFTER `stat_invitations_social`,
            ADD `stat_referrals_vip` int NOT NULL DEFAULT '0' AFTER `stat_referrals_standart`;
        ");

        $this->execute("
            update user 
            join (select parent_id,COALESCE(count(*),0) as cnt from user where packet='STANDART' group by parent_id) as t 
            set stat_referrals_standart=t.cnt 
            where user.id=t.parent_id
        ");

        $this->execute("
            update user 
            join (select parent_id,COALESCE(count(*),0) as cnt from user where packet='VIP' group by parent_id) as t 
            set stat_referrals_vip=t.cnt 
            where user.id=t.parent_id
        ");

        $this->execute("
            update user 
            join (select user_id,count(*) as cnt from invitation where type='EMAIL' group by user_id) as t 
            set stat_invitations_email=COALESCE(t.cnt,0) 
            where user.id=t.user_id
        ");

        $this->execute("
            update user 
            join (select user_id,count(*) as cnt from invitation where type='SMS' group by user_id) as t 
            set stat_invitations_sms=COALESCE(t.cnt,0) 
            where user.id=t.user_id
        ");
    }

    public function down()
    {
        echo "m161216_121847_add_new_fields cannot be reverted.\n";

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
