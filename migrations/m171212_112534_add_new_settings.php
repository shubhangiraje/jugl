<?php

use yii\db\Migration;

class m171212_112534_add_new_settings extends Migration
{
    public function up()
    {
        $this->execute("
            ALTER TABLE `user`
            ADD `setting_notification_likes` tinyint(1) NOT NULL DEFAULT '1' AFTER `setting_off_send_email`,
            ADD `setting_notification_comments` tinyint(1) NOT NULL DEFAULT '1' AFTER `setting_notification_likes`;
        ");
    }

    public function down()
    {
        echo "m171212_112534_add_new_settings cannot be reverted.\n";

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
