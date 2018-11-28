<?php

use yii\db\Migration;

class m170331_131458_add_spam_report_is_active extends Migration
{
    public function up()
    {
        $this->execute("
            ALTER TABLE `user_spam_report`
            ADD `is_active` tinyint(1) NOT NULL DEFAULT '1';
        ");

        $this->execute("
            ALTER TABLE `user_spam_report`
            CHANGE `dt` `dt` timestamp NULL AFTER `second_user_id`;
        ");
    }

    public function down()
    {
        echo "m170331_131458_add_spam_report_is_active cannot be reverted.\n";

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
