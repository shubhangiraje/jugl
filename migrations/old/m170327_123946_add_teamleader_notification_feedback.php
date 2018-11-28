<?php

use yii\db\Migration;

class m170327_123946_add_teamleader_notification_feedback extends Migration
{
    public function up()
    {
        $this->execute("
            ALTER TABLE `user`
            ADD `teamleader_feedback_notified` tinyint(1) NOT NULL DEFAULT '0';
        ");

        $this->execute("update user set teamleader_feedback_notified=1");
    }

    public function down()
    {
        echo "m170327_123946_add_teamleader_notification_feedback cannot be reverted.\n";

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
