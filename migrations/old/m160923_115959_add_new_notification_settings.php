<?php

use yii\db\Schema;
use yii\db\Migration;

class m160923_115959_add_new_notification_settings extends Migration
{
    public function up()
    {
        $this->execute("
            ALTER TABLE `user_device`
            ADD `setting_notification_offer` tinyint(1) NOT NULL DEFAULT '1' AFTER `setting_notification_money`,
            ADD `setting_notification_search_request` tinyint(1) NOT NULL DEFAULT '1' AFTER `setting_notification_offer`,
            ADD `setting_sound_request` tinyint(1) NOT NULL DEFAULT '1',
            ADD `setting_sound_search_request` tinyint(1) NOT NULL DEFAULT '1' AFTER `setting_sound_request`;
        ");

        $this->execute("
                  ALTER TABLE `user_device`
                CHANGE `setting_sound_request` `setting_sound_offer` tinyint(1) NOT NULL DEFAULT '1' AFTER `setting_sound_money`;
        ");
    }

    public function down()
    {
        echo "m160923_115959_add_new_notification_settings cannot be reverted.\n";

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
