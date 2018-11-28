<?php

use yii\db\Schema;
use yii\db\Migration;

class m151126_105547_modify_device_fields extends Migration
{
    public function up()
    {
        $this->execute("
ALTER TABLE `user_device`
DROP `setting_notification_message`,
DROP `setting_notification_sound`,
COMMENT='';
        ");

        $this->execute("
ALTER TABLE `user_device`
ADD `setting_notification_all` tinyint(1) NOT NULL DEFAULT '1',
ADD `setting_notification_chat` tinyint(1) NOT NULL DEFAULT '1' AFTER `setting_notification_all`,
ADD `setting_notification_activity` tinyint(1) NOT NULL DEFAULT '1' AFTER `setting_notification_chat`,
ADD `setting_notification_money` tinyint(1) NOT NULL DEFAULT '1' AFTER `setting_notification_activity`,
ADD `setting_sound_all` tinyint(1) NOT NULL DEFAULT '1' AFTER `setting_notification_money`,
ADD `setting_sound_chat` tinyint(1) NOT NULL DEFAULT '1' AFTER `setting_sound_all`,
ADD `setting_sound_activity` tinyint(1) NOT NULL DEFAULT '1' AFTER `setting_sound_chat`,
ADD `setting_sound_money` tinyint(1) NOT NULL DEFAULT '1' AFTER `setting_sound_activity`,
COMMENT='';
        ");
    }

    public function down()
    {
        echo "m151126_105547_modify_device_fields cannot be reverted.\n";

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
