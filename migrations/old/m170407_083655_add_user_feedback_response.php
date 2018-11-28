<?php

use yii\db\Migration;

class m170407_083655_add_user_feedback_response extends Migration
{
    public function up()
    {
        $this->execute("
ALTER TABLE `user_feedback`
CHANGE `create_dt` `create_dt` timestamp NOT NULL DEFAULT '2000-01-01 00:00:00' AFTER `rating`,
ADD `response` varchar(4096) NOT NULL,
ADD `response_dt` timestamp NULL AFTER `response`;
        ");
    }

    public function down()
    {
        echo "m170407_083655_add_user_feedback_response cannot be reverted.\n";

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
