<?php

use yii\db\Migration;

class m180914_101745_add_new_videoident_status extends Migration
{
    public function up()
    {
        $this->execute("
            ALTER TABLE `user`
            CHANGE `video_identification_status` `video_identification_status` enum('NONE','AWAITING','ACCEPTED','REJECTED','ACCEPTED_AUTO','ACCEPTED_MANUAL') COLLATE 'utf8mb4_unicode_ci' NOT NULL DEFAULT 'NONE' AFTER `is_update_country_after_login`;
        ");

        $this->execute("
            update user set video_identification_status='ACCEPTED_AUTO' where video_identification_status='ACCEPTED'
        ");

        $this->execute("
            ALTER TABLE `user`
            CHANGE `video_identification_status` `video_identification_status` enum('NONE','AWAITING','REJECTED','ACCEPTED_AUTO','ACCEPTED_MANUAL') COLLATE 'utf8mb4_unicode_ci' NOT NULL DEFAULT 'NONE' AFTER `is_update_country_after_login`;
        ");
    }

    public function down()
    {
        echo "m180914_101745_add_new_videoident_status cannot be reverted.\n";

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
