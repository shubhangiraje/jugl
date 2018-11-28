<?php

use yii\db\Migration;

class m170602_095739_add_teamleader_notification_at extends Migration
{
    public function up()
    {
        $this->execute("
ALTER TABLE `user`
ADD `teamleader_feedback_notification_at` timestamp NULL;
        ");
    }

    public function down()
    {
        echo "m170602_095739_add_teamleader_notification_at cannot be reverted.\n";

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
