<?php

use yii\db\Schema;
use yii\db\Migration;

class m160830_125047_add_user_next_invitation_notification extends Migration
{
    public function up()
    {
        $this->execute("
            ALTER TABLE `user`
            ADD `next_invitation_notification` timestamp NULL;
        ");

        $this->execute("
            ALTER TABLE `user`
            ADD INDEX `next_invitation_notification` (`next_invitation_notification`);
        ");

        $this->execute("
            ALTER TABLE `user`
            CHANGE `next_invitation_notification` `next_invitation_notification_email` timestamp NULL AFTER `stat_new_search_requests_offers`,
            ADD `next_invitation_notification_push` timestamp NULL;
        ");

        $this->execute("
            ALTER TABLE `user`
            ADD INDEX `next_invitation_notification_push` (`next_invitation_notification_push`);
        ");

        $this->execute("
            ALTER TABLE `user`
            ADD `invitation_notification_start` timestamp NULL;        
        ");

        $this->execute("
            ALTER TABLE `user`
            ADD `app_login_notifications_sent` int NOT NULL DEFAULT '0';
        ");

        $this->execute("
            update `user` set invitation_notification_start=NOW()
        ");
    }

    public function down()
    {
        echo "m160830_125047_add_user_next_invitation_notification cannot be reverted.\n";

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
