<?php

use yii\db\Schema;
use yii\db\Migration;

class m151102_095137_add_spam_report_fields extends Migration
{
    public function up()
    {
        $this->execute("
            ALTER TABLE `user_spam_report`
            ADD `object` varchar(256) NOT NULL,
            ADD `comment` varchar(1024) NOT NULL AFTER `object`,
            COMMENT='';
        ");

        $this->execute("
            update user_spam_report set object='Chat',comment='no comments'
        ");
    }

    public function down()
    {
        echo "m151102_095137_add_spam_report_fields cannot be reverted.\n";

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
